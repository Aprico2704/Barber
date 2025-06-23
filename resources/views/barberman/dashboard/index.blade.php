<x-admin-layout>
   <div id="content-container" class="content-container ml-64">
       <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
           <div class="bg-white shadow rounded-lg p-6 transform transition duration-500 hover:scale-105">
               <div class="flex items-center">
                   <div class="ml-0">
                       <h3 class="text-lg leading-6 font-medium text-gray-900">Total Reservasi</h3>
                       <p class="mt-1 text-2xl font-semibold text-gray-900">{{ $totalReservasi }}</p>
                   </div>
               </div>
           </div>
           <div class="bg-white shadow rounded-lg p-6 transform transition duration-500 hover:scale-105">
               <div class="flex items-center">
                   <div class="ml-0">
                       <h3 class="text-lg leading-6 font-medium text-gray-900">Total Pelanggan</h3>
                       <p class="mt-1 text-2xl font-semibold text-gray-900">{{ $pelangganCount }}</p>
                   </div>
               </div>
           </div>
           <div class="bg-white shadow rounded-lg p-6 transform transition duration-500 hover:scale-105">
               <div class="flex items-center">
                   <div class="ml-0">
                       <h3 class="text-lg leading-6 font-medium text-gray-900">Total Barberman</h3>
                       <p class="mt-1 text-2xl font-semibold text-gray-900">{{ $barbermanCount }}</p>
                   </div>
               </div>
           </div>
       </div>
   </div>

   <script>
       document.addEventListener('DOMContentLoaded', function() {
           const contentContainer = document.getElementById('content-container');
           contentContainer.style.opacity = 0;
           contentContainer.style.transform = 'translateY(-50px)';
           setTimeout(() => {
               contentContainer.style.transition = 'opacity 0.5s ease-out, transform 0.5s ease-out';
               contentContainer.style.opacity = 1;
               contentContainer.style.transform = 'translateY(0)';
               setTimeout(() => {
                   contentContainer.style.transition = 'transform 0.2s ease-in-out';
                   contentContainer.style.transform = 'translateY(5px)';
                   setTimeout(() => {
                       contentContainer.style.transform = 'translateY(0)';
                   }, 200);
               }, 500);
           }, 100);
       });
   </script>
</x-admin-layout>