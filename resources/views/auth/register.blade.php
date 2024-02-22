@extends('layouts.app', ['buffet'=>$buffet])

@section('content')
    @include('layouts.navbars.guest.navbar')
    <main class="main-content  mt-0">
        <div class="page-header align-items-start min-vh-50 pt-5 pb-11 m-3 border-radius-lg"
            style="background-image: url('https://raw.githubusercontent.com/creativetimofficial/public-assets/master/argon-dashboard-pro/assets/img/signup-cover.jpg'); background-position: top;">
            <span class="mask bg-gradient-dark opacity-6"></span>
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-5 text-center mx-auto">
                        <h1 class="text-white mb-2 mt-5">Bem vindo ao {{ $buffet->trading_name }}!</h1>
                        <p class="text-lead text-white">lorem ipsum</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="row mt-lg-n10 mt-md-n11 mt-n10 justify-content-center">
                <div class="col-xl-4 col-lg-5 col-md-7 mx-auto">
                    <div class="card z-index-0">
                        <div class="card-body">
                            <form method="POST" action="{{ route('register.perform', ['buffet'=>$buffet->slug]) }}" id="form">
                                @csrf
                                <div class="flex flex-col mb-3">
                                    <input type="text" name="name" class="form-control" placeholder="Nome" aria-label="Nome" value="{{ old('name') }}" >
                                    @error('name') <p class='text-danger text-xs pt-1'> {{ $message }} </p> @enderror
                                </div>
                                <div class="flex flex-col mb-3">
                                    <input type="email" name="email" class="form-control" placeholder="Email" aria-label="Email" value="{{ old('email') }}" >
                                    @error('email') <p class='text-danger text-xs pt-1'> {{ $message }} </p> @enderror
                                </div>
                                <div class="flex flex-col mb-3">
                                    <input type="text" name="document" class="form-control" placeholder="Documento" aria-label="document" value="{{ old('document') }}" id="document">
                                    @error('document') <p class='text-danger text-xs pt-1'> {{ $message }} </p> @enderror
                                    <p class='text-danger text-xs pt-1' id="document-error"></p>
                                </div>
                                <div class="flex flex-col mb-3">
                                    <input type="text" name="phone1" class="form-control" placeholder="Telefone*" aria-label="phone1" value="{{ old('phone1') }}" id="phone1">
                                    @error('phone1') <p class='text-danger text-xs pt-1'> {{ $message }} </p> @enderror
                                </div>
                                <div class="flex flex-col mb-3">
                                    <input type="password" name="password" class="form-control" placeholder="Senha" aria-label="Senha" id="password">
                                    @error('password') <p class='text-danger text-xs pt-1'> {{ $message }} </p> @enderror
                                </div>
                                <div class="flex flex-col mb-3">
                                    <input type="password" name="password_confirmation" class="form-control" placeholder="Confirmação de senha" aria-label="Confirmação de senha" id="password_confirmation">
                                    @error('password_confirmation') <p class='text-danger text-xs pt-1'> {{ $message }} </p> @enderror
                                </div>
                                <div class="form-check form-check-info text-start">
                                    <input class="form-check-input" type="checkbox" name="terms" id="flexCheckDefault" >
                                    <label class="form-check-label" for="flexCheckDefault">
                                        Eu concordo com os <a href="javascript:;" class="text-dark font-weight-bolder">Termos e Condições</a>
                                    </label>
                                    @error('terms') <p class='text-danger text-xs'> {{ $message }} </p> @enderror
                                </div>
                                <div class="text-center">
                                    <button type="submit" class="btn bg-gradient-dark w-100 my-4 mb-2">Cadastrar</button>
                                </div>
                                <p class="text-sm mt-3 mb-0">Já tem uma conta? <a href="{{ route('login', ['buffet'=>$buffet->slug]) }}"
                                        class="text-dark font-weight-bolder">Logue aqui </a></p>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    @include('layouts.footers.guest.footer')
    <script>
        const doc = document.querySelector("#document")
        const doc_error = document.querySelector("#document-error")
        const form = document.querySelector("#form")
        const phone1 = document.querySelector("#phone1")

        form.addEventListener('submit', async function (e) {
            e.preventDefault()

            // const document_valid = validarCPF(doc.value)
            // if(!document_valid) {
            //     error("O documento é invalido")
            //     return;
            // }

            if(document.querySelector("#password").value !== document.querySelector("#password_confirmation").value) {
                error("As senhas não são iguais")
                return;
            }

            this.submit();
        })



        doc.addEventListener('input', (e)=>{
            e.target.value = replaceCPF(e.target.value);
            return;
        })
        phone1.addEventListener('input', (e)=>{
            e.target.value = replacePhone(e.target.value);
            return;
        })

        doc.addEventListener('focusout', (e)=>{
            const cpf_valid = validarCPF(doc.value)
            if(!cpf_valid) {
                doc_error.innerHTML = "Documento inválido"
                return
            }
            doc_error.innerHTML = ""
            return;
        })
    </script>
@endsection
