<x-app-layout title="Nuevo director">
<form method="post" action="{{route('directors.store')}}">
    
    <input type="submit" value="Crear">
  <div class="space-y-12">
    <div class="border-b border-gray-900/10 pb-12">
      <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
        <div class="sm:col-span-4">
          <label for="username" class="block text-sm/6 font-medium text-gray-900">Name </label>
          <div class="mt-2">
            <div class="flex items-center rounded-md bg-white pl-3 outline-1 -outline-offset-1 outline-gray-300 focus-within:outline-2 focus-within:-outline-offset-2 focus-within:outline-indigo-600">
                <input id="username" type="text" name="name" placeholder="janesmith" class="block min-w-0 grow bg-white py-1.5 pr-3 pl-1 text-base text-gray-900 placeholder:text-gray-400 focus:outline-none sm:text-sm/6 @error('name') is-invalid @enderror" />
                @error('name')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror   
            </div>
          </div>
        </div>
        <div class="sm:col-span-4">
          <label for="surname" class="block text-sm/6 font-medium text-gray-900">Surname </label>
          <div class="mt-2">
            <div class="flex items-center rounded-md bg-white pl-3 outline-1 -outline-offset-1 outline-gray-300 focus-within:outline-2 focus-within:-outline-offset-2 focus-within:outline-indigo-600">
                <input id="username" type="text" name="surname" placeholder="janesmith" class="block min-w-0 grow bg-white py-1.5 pr-3 pl-1 text-base text-gray-900 placeholder:text-gray-400 focus:outline-none sm:text-sm/6 @error('surname') is-invalid @enderror" />
                @error('surname')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror   
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
    </form>
</x-app-layout>