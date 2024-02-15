<div class="px-4 pt-4">
    @if ($message = session()->has('succes'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <p class="text-white mb-0">{{ session()->get('succes') }}</p>
        </div>
    @endif
    @if ($errors->any())
        @foreach ($errors->all() as $error)
            <div class="alert alert-danger alert-component" role="alert">
                <p class="text-white mb-0">{{ $error }}</p>
            </div>
        @endforeach
    @endif
</div>

<script>
    const alert_component = document.querySelector('#alert');
    setTimeout(() => {
        console.log(alert_component)
        alert_component.innerHTML = ""
    }, 6000);
</script>