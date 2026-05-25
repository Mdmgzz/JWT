<div class="relative overflow-x-auto bg-neutral-primary-soft shadow-xs rounded-base border border-default">
    <table class="w-full text-sm text-left rtl:text-right text-body">
        <thead class="text-sm text-body bg-neutral-secondary-soft border-b rounded-base border-default">
            <tr>
                @foreach($header as $headerTitle)
                    <th scope="col" class="px-6 py-3 font-medium">
                        {{ $headerTitle }}
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            
            @foreach($tableData as $rowData)
                <tr class="bg-neutral-primary border-b border-default">
                    @foreach($rowData as $valor)
                        <td class="px-6 py-4">{{ $valor }}</td>
                    @endforeach
                </tr>
            @endforeach
            

        </tbody>
    </table>
</div>