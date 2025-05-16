<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Comenzar Quiz Bíblico</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f0f4f8, #d9e2ec);
            height: 100vh;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #344e41;
        }

        .inicio-container {
            background: #ffffff;
            padding: 40px 50px;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
            max-width: 450px;
            width: 100%;
            text-align: center;
            animation: slideUpFade 0.6s ease forwards;
        }

        h2 {
            font-size: 28px;
            margin-bottom: 15px;
            font-weight: 700;
            color: #1e293b;
        }

        p {
            font-size: 18px;
            color: #475569;
            margin-bottom: 30px;
        }

        a {
            display: inline-block;
            padding: 14px 36px;
            background-color: #3b82f6;
            color: white;
            font-weight: 600;
            border-radius: 10px;
            text-decoration: none;
            box-shadow: 0 6px 12px rgba(59, 130, 246, 0.5);
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        a:hover {
            background-color: #2563eb;
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(37, 99, 235, 0.6);
        }

        @keyframes slideUpFade {
            0% {
                opacity: 0;
                transform: translateY(25px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <div class="inicio-container">
        <h2>Hola <?php echo htmlspecialchars($_SESSION["usuario_nombre"]); ?> 👋</h2>
        <p>¿Estás listo para comenzar el Quiz Bíblico?</p>
        <a href="quiz.php">Comenzar</a>
    </div>
</body>
</html>
