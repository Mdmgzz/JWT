<x-app-layout title="Director: {{$director->name}}">
   <div class="relative max-w-md mx-auto md:max-w-2xl mt-6 min-w-0 break-words bg-white w-full mb-6 shadow-lg rounded-xl mt-16">
    <div class="px-6">
        <div class="flex flex-wrap justify-center">
            <div class="w-full text-center mt-20">
                <div class="flex justify-center lg:pt-4 pt-8 pb-0">
                    <div class="p-3 text-center">
                        <span class="text-xl font-bold block uppercase tracking-wide text-slate-700">3,360</span>
                        <span class="text-sm text-slate-400">Películas</span>
                    </div>
                    
                </div>
            </div>
        </div>
        <div class="text-center mt-2">
            <h3 class="text-2xl text-slate-700 font-bold leading-normal mb-1">{{$director->name}} {{$director->surname}}</h3>
            <div class="text-xs mt-0 mb-2 text-slate-400 font-bold uppercase">
                <i class="fas fa-map-marker-alt mr-2 text-slate-400 opacity-75"></i>Paris, France
            </div>
        </div>
        <div class="mt-6 py-6 border-t border-slate-200 text-center">
            <div class="flex flex-wrap justify-center">
                <div class="w-full px-4">
                    <p class="font-light leading-relaxed text-slate-600 mb-4">Descripción genérica de aldeano genérico Descripción genérica de aldeano genérico Descripción genérica de aldeano genérico Descripción genérica de aldeano genérico Descripción genérica de aldeano genérico.</p>

                    <div class="tablaPeliculas">
                        <x-table :header="$headerPeliculas" :tableData="$tableData"></x-table>
                    </div>
                    <a href="{{route('directors.index')}}">Volver</a>
                </div>
            </div>
        </div>
    </div>
</div>


</x-app-layout>