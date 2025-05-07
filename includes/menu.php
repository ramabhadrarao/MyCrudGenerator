<nav class="bg-blue-500 p-4 shadow-md">
    <div class="container mx-auto flex justify-between items-center">
        <!-- Dashboard Link -->
        <div class="flex items-center space-x-4">
            <a href="../pages/dashboard.php" class="text-white font-semibold hover:text-blue-200 transition-colors duration-200">Menu</a>
        </div>
        <!-- User Actions -->
        <div class="flex items-center space-x-4 relative">
            <a href="../pages/dashboard.php?page=change_password" class="text-white hover:text-blue-200 transition-colors duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 1.75a8.25 8.25 0 0 0-8.25 8.25v3.5H4.75a1 1 0 0 1 0 2H7.5a.25.25 0 0 0 .25-.25v-8.25a7.25 7.25 0 1 1 14.5 0v8.25c0 .138.112.25.25.25h2.75a1 1 0 0 1 0 2H19.5v3.5a1 1 0 0 1-1 1H14.75v-4.75a2.25 2.25 0 0 0-4.5 0v4.75H5.5a1 1 0 0 1-1-1v-3.5H4.75a1 1 0 0 1 0-2H6v-3.5a8.25 8.25 0 0 0-8.25-8.25V10H4v-.25a7.25 7.25 0 0 1 14.5 0v3.5a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1v-3.5a7.25 7.25 0 1 1 14.5 0V14H19.5a.25.25 0 0 0 .25-.25v-8.25a8.25 8.25 0 0 0-8.25-8.25zM10.25 18v3.75a1.25 1.25 0 1 0 2.5 0V18h-2.5z"/>
                </svg>
            </a>
            <a href="../pages/logout.php" class="text-white hover:text-blue-200 transition-colors duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M16 17l4-4-4-4M12 12h8"/>
                    <path d="M2 12h10"/>
                </svg>
            </a>
            <div class="relative" x-data="{ open: false }" @mouseover="open = true" @mouseleave="open = false">
                <button class="flex items-center space-x-2 text-white">
                    <img src="../images/user-icon.gif" alt="User Icon" class="h-8 w-8 rounded-full">
                </button>
            </div>
        </div>
    </div>
</nav>