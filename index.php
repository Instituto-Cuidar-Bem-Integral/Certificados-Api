<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Certificados - Instituto Cuidar Bem</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        header {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        header h1 {
            color: white;
            font-size: 2rem;
            margin-bottom: 5px;
        }

        header p {
            color: rgba(255, 255, 255, 0.8);
            font-size: 1rem;
        }

        main {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px 20px;
        }

        .menu-container {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 600px;
            width: 100%;
        }

        .menu-title {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
            font-size: 1.5rem;
        }

        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .menu-item {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 15px;
            padding: 30px 20px;
            text-align: center;
            text-decoration: none;
            color: white;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 15px;
        }

        .menu-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(102, 126, 234, 0.4);
        }

        .menu-item .icon {
            font-size: 3rem;
        }

        .menu-item h3 {
            font-size: 1.2rem;
            margin-bottom: 5px;
        }

        .menu-item p {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        footer {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 20px;
            text-align: center;
            color: rgba(255, 255, 255, 0.8);
            border-top: 1px solid rgba(255, 255, 255, 0.2);
        }

        @media (max-width: 768px) {
            .menu-grid {
                grid-template-columns: 1fr;
            }
            
            header h1 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <header>
        <h1>🎓 Sistema de Certificados</h1>
        <p>Instituto Cuidar Bem</p>
    </header>

    <main>
        <div class="menu-container">
            <h2 class="menu-title">📋 Menu Principal</h2>
            
            <div class="menu-grid">
                <a href="cadastrar.php" class="menu-item">
                    <div class="icon">📝</div>
                    <h3>Cadastrar</h3>
                    <p>Novo certificado</p>
                </a>

                <a href="listar.php" class="menu-item">
                    <div class="icon">📄</div>
                    <h3>Listar</h3>
                    <p>Ver certificados</p>
                </a>

                <a href="validar.php" class="menu-item">
                    <div class="icon">✅</div>
                    <h3>Validar</h3>
                    <p>Verificar certificado</p>
                </a>
            </div>
        </div>
    </main>

    <footer>
        <p>© 2026 Instituto Cuidar Bem - Todos os direitos reservados</p>
    </footer>
</body>
</html>