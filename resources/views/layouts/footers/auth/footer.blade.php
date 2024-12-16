<footer class="footer pt-3 dont-copy">
    <div class="container-fluid">
        <div class="row align-items-center justify-content-lg-between">
            <div class="col-lg-6 mb-lg-0 mb-4">
                <div class="copyright text-center text-sm text-muted text-lg-start">
                    ©
                    <script>
                        document.write(new Date().getFullYear())
                    </script>,
                    feito com <i class="fa fa-heart"></i> por
                    <a href="{{ config('app.administrative_url') }}" class="font-weight-bold" target="_blank">BuffeTTech</a>.
                </div>
            </div>
            <div class="col-lg-6">
                <ul class="nav nav-footer justify-content-center justify-content-lg-end">
                    <li class="nav-item">
                        <a href="{{ config('app.administrative_url') }}" class="nav-link text-muted" target="_blank">BuffeTTech</a>
                    </li>
                    <li class="nav-item">
                        <a href="https://github.com/BuffeTTech" class="nav-link text-muted" target="_blank">GitHub</a>
                    </li>
                    <li class="nav-item">
                        <a href="https://www.linkedin.com/company/buffettech" class="nav-link text-muted" target="_blank">Linkedin</a>
                    </li>
                    {{-- <li class="nav-item">
                        <a href="#" class="nav-link text-muted" target="_blank">Instagram</a>
                    </li> --}}
                    {{-- <li class="nav-item">
                        <a href="#" class="nav-link pe-0 text-muted" target="_blank">Link 4</a>
                    </li> --}}
                </ul>
            </div>
        </div>
    </div>
</footer>
