{{-- Formulaire de connexion --}}
<div class="p-6 md:w-md md:min-w-[460px] flex flex-col items-center">
    <h2 class="text-2xl font-bold mb-6">Connexion</h2>
    
    <form id="login-form" class="space-y-4 min-w-[320px] w-min">
        <!-- Email -->
        <div class="form-control flex flex-col">
            <label class="label">
                <span class="label-text">Email</span>
            </label>
            <input type="text" id="email" placeholder="votre.email@exemple.com" class="input input-bordered" required />
        </div>
        
        <!-- Mot de passe -->
        <div class="form-control flex flex-col">
            <label class="label">
                <span class="label-text">Mot de passe</span>
            </label>
            <input type="password" id="password" placeholder="••••••••" class="input input-bordered" required />
            <a href="#" class="label-text-alt link link-hover">Mot de passe oublié ?</a>
        </div>
        
        <!-- Remember me -->
        <div class="form-control">
            <label class="label cursor-pointer justify-start gap-3">
                <input type="checkbox" id="remember-me" class="checkbox checkbox-primary" />
                <span class="label-text">Se souvenir de moi</span>
            </label>
        </div>
        
        <!-- Bouton de connexion -->
        <div class="form-control mt-6 w-full flex">
            <button type="submit" class="btn btn-primary m-auto">Se connecter</button>
        </div>
    </form>
    
    <!-- Séparateur -->
    <div class="divider">OU</div>
    
    <!-- Connexion avec réseaux sociaux -->
    @include('components.auth.social-login')
</div>
