<template>
    <footer class="py-12 w-full mt-10">
        <div class="max-w-7xl w-full mx-auto flex items-center justify-between">
            <div class="cursor-pointer" @click="navigateTo('/')">
                <p class="text-xl text-primary font-semibold">MatchRoom</p>
            </div>
            <nav class="flex items-center">
                <ul class="flex items-center h-full gap-2">
                    <li><ULink to="/match">Match</ULink></li>
                    <li><ULink to="/explore">Explorer</ULink></li>
                    <li><ULink to="/explore">Conditions générales</ULink></li>
                    <li><ULink to="/explore">Confidentialité</ULink></li>
                </ul>
            </nav>
            <div>
                <p class="text-quaternary text-right">© 2025 MatchRoom</p>
            </div>
        </div>
    </footer>
</template>