<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PasswordResetToken;
use App\Mail\ResetPasswordMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Rediriger selon le rôle
            return match(Auth::user()->role) {
                'admin' => redirect()->intended('/admin/dashboard'),
                'doctor' => redirect()->intended('/doctor/dashboard'),
                'patient' => redirect()->intended('/patient/dashboard'),
                default => redirect()->intended('/')
            };
        }

        return back()->withErrors([
            'email' => 'Les informations de connexion ne correspondent pas.',
        ])->onlyInput('email');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email:rfc,dns', 'max:255', 'unique:users'],
            'password' => ['required', 'min:8'],
            'role' => ['required', 'in:admin,doctor,patient'],
            'speciality' => ['nullable', 'string', 'max:255'],
        ], [
            'email.email' => 'Veuillez entrer une adresse email valide (exemple@gmail.com).',
            'email.unique' => 'Cette adresse email est déjà utilisée.',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'speciality' => $request->speciality,
        ]);

        Auth::login($user);

        // Rediriger selon le rôle avec message de succès
        return match($user->role) {
            'admin' => redirect('/admin/dashboard')->with('success', 'Compte créé avec succès !'),
            'doctor' => redirect('/doctor/dashboard')->with('success', 'Compte créé avec succès !'),
            'patient' => redirect('/patient/dashboard')->with('success', 'Compte créé avec succès !'),
            default => redirect('/')->with('success', 'Compte créé avec succès !')
        };
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email:rfc,dns', 'exists:users,email'],
        ], [
            'email.email' => 'Veuillez entrer une adresse email valide (exemple@gmail.com).',
            'email.exists' => 'Cette adresse email n\'est pas enregistrée dans notre système.',
        ]);

        $user = User::where('email', $request->email)->first();
        
        // Supprimer les anciens tokens pour cet email
        PasswordResetToken::where('email', $request->email)->delete();
        
        // Générer un nouveau token
        $token = Str::random(64);
        
        // Sauvegarder le token
        PasswordResetToken::create([
            'email' => $request->email,
            'token' => Hash::make($token),
            'created_at' => Carbon::now()
        ]);
        
        // Envoyer l'email
        try {
            // Vérifier si on est en mode log (pour les tests)
            if (config('mail.default') === 'log') {
                // En mode log, créer un lien de test
                $resetUrl = url('/reset-password/' . $token . '?email=' . urlencode($request->email));
                \Log::info('Email de réinitialisation (MODE TEST)', [
                    'email' => $request->email,
                    'reset_url' => $resetUrl,
                    'user' => $user->name
                ]);
                
                return back()->with([
                    'status' => 'reset-link-sent',
                    'test_mode' => true,
                    'reset_url' => $resetUrl,
                    'message' => 'Mode test activé - Le lien de réinitialisation est affiché ci-dessous'
                ]);
            }
            
            // Envoyer l'email normalement
            Mail::to($request->email)->send(new ResetPasswordMail($token, $request->email, $user));
            return back()->with('status', 'reset-link-sent');
            
        } catch (\Exception $e) {
            // Logger l'erreur détaillée
            \Log::error('Erreur envoi email reset password', [
                'error' => $e->getMessage(),
                'email' => $request->email,
                'mail_config' => [
                    'mailer' => config('mail.default'),
                    'host' => config('mail.mailers.smtp.host'),
                    'port' => config('mail.mailers.smtp.port'),
                    'username' => config('mail.mailers.smtp.username') ? 'SET' : 'NOT SET',
                    'password' => config('mail.mailers.smtp.password') ? 'SET' : 'NOT SET',
                ]
            ]);
            
            // En mode développement, afficher l'erreur détaillée
            if (config('app.debug')) {
                return back()->withErrors([
                    'email' => 'Erreur email: ' . $e->getMessage() . ' | Vérifiez les logs pour plus de détails.'
                ]);
            }
            
            // En production, message générique
            return back()->withErrors([
                'email' => 'Erreur lors de l\'envoi de l\'email. Contactez l\'administrateur.'
            ]);
        }
    }

    public function showResetForm($token, Request $request)
    {
        $email = $request->query('email');
        
        if (!$email) {
            return redirect()->route('password.request')->withErrors(['email' => 'Email manquant.']);
        }

        // Vérifier si le token existe et n'est pas expiré (60 minutes)
        $resetRecord = PasswordResetToken::where('email', $email)
            ->where('created_at', '>', Carbon::now()->subMinutes(60))
            ->first();

        if (!$resetRecord) {
            return redirect()->route('password.request')->withErrors(['email' => 'Ce lien de réinitialisation est invalide ou a expiré.']);
        }

        return view('auth.reset-password', compact('token', 'email'));
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:8|confirmed',
        ]);

        // Vérifier le token
        $resetRecord = PasswordResetToken::where('email', $request->email)
            ->where('created_at', '>', Carbon::now()->subMinutes(60))
            ->first();

        if (!$resetRecord || !Hash::check($request->token, $resetRecord->token)) {
            return back()->withErrors(['email' => 'Ce lien de réinitialisation est invalide ou a expiré.']);
        }

        // Mettre à jour le mot de passe
        $user = User::where('email', $request->email)->first();
        $user->update([
            'password' => Hash::make($request->password)
        ]);

        // Supprimer le token utilisé
        PasswordResetToken::where('email', $request->email)->delete();

        return redirect()->route('login')->with('status', 'password-reset');
    }
}