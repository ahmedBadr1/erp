<x-authentication-layout>
    <h1 class="text-3xl text-slate-800 dark:text-slate-100 font-bold mb-6">{{ __('Welcome back!') }} ✨</h1>
    @if (session('status'))
        <div class="mb-4 font-medium text-sm text-green-600">
            {{ session('status') }}
        </div>
    @endif
    <!-- Form -->
    <form method="POST" action="{{ route('admin.login') }}">
        @csrf
        <div class="space-y-4">
            <x-input.inner-label  type="email" name="email" :value="old('email')" required="true" autofocus="true" label="{{ __('Email') }}" />
            <x-input.inner-label  type="password" name="password" :value="old('password')" required="true" auto label="{{ __('Password') }}" />
        </div>
        <div class="flex items-center justify-between mt-6">
            @if (Route::has('password.request'))
                <div class="mr-1">
                    <a class="text-sm underline hover:no-underline" href="{{ route('password.request') }}">
                        {{ __('Forgot Password?') }}
                    </a>
                </div>
            @endif
            <x-button class="ml-3" type="submit">
                {{ __('Sign in') }}
            </x-button>
        </div>
    </form>
    <x-validation-errors class="mt-4" />
    <!-- Footer -->
    <div class="pt-5 mt-6 border-t border-slate-200">
        <div class="text-sm">
            {{ __('Don\'t you have an account?') }} <a class="font-medium text-indigo-500 hover:text-indigo-600" href="{{ route('admin.register') }}">{{ __('Sign Up') }}</a>
        </div>
        <!-- Warning -->
        <div class="mt-5">
            <x-note color="amber" >
                To support you during the pandemic super pro features are free until March 31st.
            </x-note>

        </div>
    </div>
</x-authentication-layout>
