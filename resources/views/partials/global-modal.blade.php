<div x-data="imageModal">
    <!-- Modal -->
    <div
        x-show="showModal"
        x-transition.opacity
        class="fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-50"
    >
        <div class="bg-white rounded-lg shadow-lg max-w-3xl w-full p-6 relative">
            <button @click="closeModal" class="absolute top-2 right-2 text-gray-700">&times;</button>
            <img :src="selectedImage?.url" class="w-full h-auto rounded-lg mb-4">
            <h3 class="text-xl font-bold" x-text="selectedImage?.title"></h3>
            <p class="text-gray-600" x-text="selectedImage?.artist"></p>
        </div>
    </div>
</div>
