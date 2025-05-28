<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Mini ERP')</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        body { background-color: #f8f9fa; }
        .navbar-brand { font-weight: bold; }
        .card { border: none; box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075); }
        .btn { font-weight: 500; }
        #carrinho-badge { font-size: 0.7rem; min-width: 1.2rem; height: 1.2rem; line-height: 1.2rem; }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="{{ route('produtos.index') }}">
                <i class="bi bi-shop"></i> Mini ERP
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('produtos.index') }}">
                            <i class="bi bi-box"></i> Produtos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('produtos.create') }}">
                            <i class="bi bi-plus-circle"></i> Novo Produto
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('webhook.teste') }}">
                            <i class="bi bi-webhook"></i> Webhook
                        </a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link position-relative" href="{{ route('carrinho.index') }}">
                            <i class="bi bi-cart3"></i> Carrinho
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="carrinho-badge">
                                {{ session('carrinho') ? array_sum(array_column(session('carrinho'), 'quantidade')) : '' }}
                            </span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Alerts -->
    <div class="container mt-3">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle"></i>
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    </div>

    <!-- Main Content -->
    <main class="container my-4">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-light py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>Mini ERP</h5>
                    <p class="text-muted">Sistema de controle de Pedidos, Produtos, Cupons e Estoque</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="text-muted">
                        <i class="bi bi-code-slash"></i> Desenvolvido com Laravel + Bootstrap
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script>
        // Configurar CSRF token para AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Auto-hide alerts
        $('.alert').each(function() {
            var alert = $(this);
            setTimeout(function() {
                alert.fadeOut();
            }, 5000);
        });

        // Função para mostrar alertas
        function showAlert(type, message) {
            var iconClass = type === 'success' ? 'bi-check-circle' : 'bi-exclamation-triangle';
            var alert = $('<div class="alert alert-' + type + ' alert-dismissible fade show" role="alert">' +
                          '<i class="bi ' + iconClass + '"></i> ' + message +
                          '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                          '</div>');
            
            $('.container').first().prepend(alert);
            
            setTimeout(function() {
                alert.fadeOut();
            }, 5000);
        }

        // Função para atualizar badge do carrinho
        function updateCartBadge(quantity) {
            var badge = $('#carrinho-badge');
            if (quantity > 0) {
                badge.text(quantity).show();
            } else {
                badge.hide();
            }
        }

        // Função para formatar moeda
        function formatCurrency(value) {
            return new Intl.NumberFormat('pt-BR', {
                style: 'currency',
                currency: 'BRL'
            }).format(value);
        }
    </script>
    
    @stack('scripts')
</body>
</html>
