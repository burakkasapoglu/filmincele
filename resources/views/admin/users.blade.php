@extends('admin.layout')
@section('admin-content')
<div>
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-white">Üyeler</h1>
            <p class="text-gray-500 text-sm mt-1">{{ $users->total() }} kayıtlı üye</p>
        </div>
    </div>

    <div class="bg-gray-900 rounded-2xl border border-gray-800/50 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-800/50 text-gray-500 text-xs uppercase tracking-wider">
                        <th class="text-left px-5 py-4 font-medium">Üye</th>
                        <th class="text-left px-5 py-4 font-medium hidden md:table-cell">Kayıt Tarihi</th>
                        <th class="text-center px-5 py-4 font-medium">Puan</th>
                        <th class="text-center px-5 py-4 font-medium">Liste</th>
                        <th class="text-center px-5 py-4 font-medium hidden md:table-cell">Rol</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800/50">
                    @foreach($users as $user)
                        <tr class="hover:bg-gray-800/30 transition">
                            <td class="px-5 py-4">
                                <a href="{{ route('admin.users.show', $user) }}" class="flex items-center gap-3 group">
                                    <div class="w-9 h-9 rounded-full bg-gradient-to-br from-rose-600/20 to-rose-600/10 flex items-center justify-center text-rose-400 text-xs font-semibold">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="text-white group-hover:text-rose-400 transition">{{ $user->name }}</p>
                                        <p class="text-gray-500 text-xs">{{ $user->email }}</p>
                                    </div>
                                </a>
                            </td>
                            <td class="px-5 py-4 text-gray-400 text-xs hidden md:table-cell">{{ $user->created_at->format('d.m.Y') }}</td>
                            <td class="px-5 py-4 text-center">
                                <span class="text-yellow-400 font-medium">{{ $user->ratings_count }}</span>
                            </td>
                            <td class="px-5 py-4 text-center">
                                <span class="text-white">{{ $user->watchlists_count }}</span>
                            </td>
                            <td class="px-5 py-4 text-center hidden md:table-cell">
                                @if($user->is_admin)
                                    <span class="inline-flex px-2.5 py-0.5 bg-rose-600/20 text-rose-400 text-xs rounded-full font-medium">Admin</span>
                                @else
                                    <span class="inline-flex px-2.5 py-0.5 bg-gray-800 text-gray-500 text-xs rounded-full">Üye</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">{{ $users->links() }}</div>
</div>
@endsection
