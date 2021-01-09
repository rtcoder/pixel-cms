<form action="" method="get">
    <input type="text"
           placeholder="@lang('common.search')"
           name="searchTerm"
           value="{{ $searchTerm ?? '' }}">
    <button type="submit">
        <span class="material-icons">search</span>
    </button>
</form>
