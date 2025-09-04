<div x-data="languageSwitcher()" class="flex items-center space-x-2">
    <select x-model="currentLang" @change="switchLanguage($event.target.value)"
            class="border border-gray-300 rounded-md px-3 py-1 text-sm">
        <option value="en" :selected="currentLang === 'en'">English</option>
        <option value="ar" :selected="currentLang === 'ar'">العربية</option>
    </select>
</div>
