<x-layout>
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <x-topbar /> <!-- Include the top bar -->

    <div class="dashboard-container">
        <h1>Welcome to Your Dashboard, {{ Auth::user()->name }}!</h1>

        <!-- Search Form -->
        <div class="search-container">
            <form method="GET" action="{{ route('journal.search') }}">
                <input type="text" name="query" placeholder="Search by date or content" required>
                <button type="submit">Search</button>
            </form>
        </div>

        <!-- Filter for Favorite Entries -->
        <div class="filter-favorites">
            <form method="GET" action="{{ route('journal.favorites') }}">
                <button type="submit">Show Favorite Entries</button>
            </form>
        </div>

        <div class="journals">
            <h2>Your Journal Entries</h2>

            @if($journalEntries->isEmpty())
                <p>You have no journal entries yet. Start writing your thoughts!</p>
            @else
                <ul>
                    @foreach($journalEntries as $entry)
                        <li>
                            <strong>{{ \Carbon\Carbon::parse($entry->date)->format('F j, Y') }}:</strong>
                            <!-- Render the content generated by Editor.js -->
                            <div class="journal-content">
                                @php
                                    $contentBlocks = json_decode($entry->content, true);
                                @endphp

                                @if(is_array($contentBlocks) && isset($contentBlocks['blocks']))
                                    @foreach($contentBlocks['blocks'] as $block)
                                        @if($block['type'] == 'paragraph')
                                            <p>{{ $block['data']['text'] }}</p>
                                        @elseif($block['type'] == 'list')
                                            <ul>
                                                @foreach($block['data']['items'] as $item)
                                                    <li>{{ $item }}</li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    @endforeach
                                @else
                                    <p>No content available for this entry.</p>
                                @endif
                            </div>

                            <!-- Edit Button -->
                            <a href="{{ route('journal.edit', $entry->id) }}" class="btn-edit">Edit</a>
                            
                            <!-- Toggle Favorite Form -->
                            <form action="{{ route('journal.toggleFavorite', $entry->id) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn-favorite">
                                    {{ $entry->is_favorite ? 'Unfavorite' : 'Favorite' }}
                                </button>
                            </form>
                
                            <!-- Delete Form -->
                            <form action="{{ route('journal.destroy', $entry->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-delete" onclick="return confirm('Are you sure you want to delete this entry?');">Delete</button>
                            </form>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        <a href="{{ route('journal.create') }}" class="btn-write">Write a New Journal Entry</a>
    </div>
</x-layout>
