<template>
    <footer class="py-8 sm:py-12 w-full mt-10">
        <div class="max-w-7xl w-full mx-auto px-4 sm:px-6">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-6 sm:gap-0">
                <div class="cursor-pointer" @click="navigateTo('/')">
                    <p class="text-xl text-primary font-semibold">MatchRoom</p>
                </div>
                <nav class="flex items-center">
                    <ul class="flex flex-wrap items-center justify-center gap-4 sm:gap-2">
                        <li><ULink to="/match">Match</ULink></li>
                        <li><ULink to="/explore">Explorer</ULink></li>
                        <li><ULink to="/explore">Conditions générales</ULink></li>
                        <li><ULink to="/explore">Confidentialité</ULink></li>
                    </ul>
                </nav>
                <div class="mt-4 sm:mt-0">
                    <p class="text-quaternary text-center sm:text-right">© 2025 MatchRoom</p>
                </div>
            </div>
        </div>
    </footer>
</template>