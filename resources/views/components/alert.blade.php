<div>
    @if ($message = session()->has('success'))
        <div class="px-4 pt-4">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <p class="text-white mb-0">{{ session()->get('success') }}</p>
            </div>
        </div>
    @endif
    @if ($errors->any())
        @foreach ($errors->all() as $error)
            <div class="px-4 pt-4">
                <div class="alert alert-danger alert-component alert-dismissible fade show" role="alert">
                    <p class="text-white mb-0">{{ $error }}</p>
                </div>
            </div>
        @endforeach
    @endif
</div>

<script>
    const alert_component = document.querySelector('#alert');
    setTimeout(() => {
        alert_component.innerHTML = ""
    }, 6000);
</script>