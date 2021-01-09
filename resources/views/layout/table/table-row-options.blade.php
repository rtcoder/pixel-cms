<div class="row-options">
    <a href="{{url()->current()}}/{{ $row->id }}">
        <span class="material-icons edit">edit</span>
    </a>

    @if($canDelete ?? true)
        <a href="">
            <span class="material-icons delete">delete</span>
        </a>
    @endif

</div>
