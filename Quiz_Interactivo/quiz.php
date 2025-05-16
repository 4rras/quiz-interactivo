<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

include 'conexion.php';

$usuario_id = $_SESSION['usuario_id'];

// Verificamos si el usuario ya completó el quiz
$sql_check = "SELECT completado FROM usuarios WHERE id = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("i", $usuario_id);
$stmt_check->execute();
$stmt_check->bind_result($completado);
$stmt_check->fetch();
$stmt_check->close();

if ($completado == 1) {
    $mensajeYaJugado = true;
} else {
    $mensajeYaJugado = false;
}


// Si no completó, cargamos las preguntas normalmente
$sql = "SELECT * FROM preguntas_biblicas ORDER BY id ASC LIMIT 25";
$resultado = $conn->query($sql);

$preguntas = [];
if ($resultado && $resultado->num_rows > 0) {
    while ($fila = $resultado->fetch_assoc()) {
        $preguntas[] = $fila;
    }
} else {
    die("No se encontraron preguntas.");
}

$preguntas_json = json_encode($preguntas);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Quiz Bíblico</title>
    
    <style>
       
      body {
  display: flex;
  justify-content: center;  /* centra horizontalmente */
  align-items: center;      /* centra verticalmente */
  height: 100vh;            /* altura 100% viewport */
  margin: 0;                /* elimina márgenes por defecto */
  background: linear-gradient(135deg, #667eea, #764ba2);
  font-family: 'Poppins', sans-serif;
  color: #fff;
}


      .contenedor {
  background: rgba(255, 255, 255, 0.1);
  padding: 40px 50px;
  border-radius: 15px;
  box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
  backdrop-filter: blur(10px);
  -webkit-backdrop-filter: blur(10px);
  width: 90%;
  max-width: 700px;
  text-align: center;
  box-sizing: border-box;
}


        h2 {
            font-weight: 600;
            font-size: 28px;
            margin-bottom: 15px;
            text-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }

        .cronometro {
            font-weight: 700;
            font-size: 18px;
            margin-bottom: 20px;
            color: #ffb347;
            text-shadow: 1px 1px 3px #00000080;
        }

        #pregunta-texto {
            font-size: 22px;
            margin-bottom: 25px;
            min-height: 70px;
            line-height: 1.3;
            text-shadow: 0 1px 3px rgba(0,0,0,0.4);
        }

        .opcion {
            display: block;
            background: rgba(255,255,255,0.15);
            border-radius: 8px;
            padding: 15px 20px;
            margin: 12px 0;
            cursor: pointer;
            transition: background-color 0.3s ease;
            font-size: 18px;
            box-shadow: 0 4px 6px rgb(0 0 0 / 0.1);
            user-select: none;
            color: #fff;
        }

        .opcion:hover {
            background: rgba(255, 179, 71, 0.8);
            color: #000;
        }

        .opcion input[type="radio"] {
            margin-right: 12px;
            cursor: pointer;
            transform: scale(1.2);
        }

        #btnSiguiente {
            margin-top: 25px;
            padding: 12px 35px;
            background-color: #ffb347;
            border: none;
            border-radius: 30px;
            font-weight: 600;
            font-size: 18px;
            color: #000;
            cursor: pointer;
            transition: background-color 0.3s ease;
            box-shadow: 0 4px 10px rgba(255,179,71,0.7);
        }

        #btnSiguiente:hover {
            background-color: #ff9e00;
            box-shadow: 0 6px 14px rgba(255,158,0,0.9);
        }

        .final {
            font-size: 26px;
            font-weight: 600;
            text-shadow: 1px 1px 4px #000;
            margin-top: 20px;
        }

        @media (max-width: 480px) {
            .contenedor {
                padding: 30px 25px;
            }

            h2 {
                font-size: 24px;
            }

            #pregunta-texto {
                font-size: 18px;
            }

            .opcion {
                font-size: 16px;
                padding: 12px 15px;
            }

            #btnSiguiente {
                padding: 10px 25px;
                font-size: 16px;
            }
        }
        
    </style>
</head>
<body>
    <?php if ($mensajeYaJugado): ?>
    <style>
        .ya-jugado {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: #fff;
            font-family: 'Poppins', sans-serif;
        }

        .ya-jugado .mensaje {
            background: rgba(255, 255, 255, 0.1);
            padding: 40px 50px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.4);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            animation: fadeIn 1s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>

    <div class="ya-jugado">
        <div class="mensaje">
            <h2>Ya participaste en este quiz</h2>
            <p>Gracias por tu interés 🙏. ¡Te esperamos para el próximo!</p>
            <a href="logout.php" style="display:inline-block;margin-top:20px;padding:10px 20px;background:#ffb347;color:#000;border-radius:30px;text-decoration:none;font-weight:600;">Cerrar sesión</a>
        </div>
    </div>
    <?php exit(); ?>
<?php endif; ?>

    <div class="contenedor">
        <h2 id="pregunta-numero"></h2>
        <p class="cronometro">Tiempo restante: <span id="tiempo">20</span> segundos</p>
        <div id="pregunta-texto"></div>
        <div id="opciones"></div>
        <button id="btnSiguiente" onclick="forzarSiguiente()">Siguiente</button>
    </div>

   <script>
    const preguntas = <?php echo $preguntas_json; ?>;
    let indice = parseInt(localStorage.getItem('quizIndice')) || 0;
    let tiempoRestante = parseInt(localStorage.getItem('quizTiempo')) || 60;
    let intervalo;

    function mostrarPregunta() {
        if (indice >= preguntas.length) {
            mostrarFinal();
            return;
        }

        const preguntaActual = preguntas[indice];
        document.getElementById("pregunta-numero").textContent = `Pregunta ${indice + 1} de ${preguntas.length}`;
        document.getElementById("pregunta-texto").textContent = preguntaActual.pregunta;

        const opcionesHTML = `
            <label class="opcion"><input type="radio" name="opcion" value="${preguntaActual.opcion1}"> ${preguntaActual.opcion1}</label>
            <label class="opcion"><input type="radio" name="opcion" value="${preguntaActual.opcion2}"> ${preguntaActual.opcion2}</label>
            <label class="opcion"><input type="radio" name="opcion" value="${preguntaActual.opcion3}"> ${preguntaActual.opcion3}</label>
        `;

        document.getElementById("opciones").innerHTML = opcionesHTML;
        document.getElementById("tiempo").textContent = tiempoRestante;

        intervalo = setInterval(() => {
            tiempoRestante--;
            document.getElementById("tiempo").textContent = tiempoRestante;
            localStorage.setItem('quizTiempo', tiempoRestante); // guarda tiempo restante

            if (tiempoRestante <= 0) {
                siguientePregunta();
            }
        }, 1000);
    }

    function guardarRespuesta() {
        clearInterval(intervalo);

        const opciones = document.getElementsByName("opcion");
        let seleccion = "";

        for (let opcion of opciones) {
            if (opcion.checked) {
                seleccion = opcion.value;
                break;
            }
        }

        if (seleccion !== "") {
            const preguntaActual = preguntas[indice];
            fetch('actualizar_puntos.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `pregunta_id=${preguntaActual.id}&respuesta=${encodeURIComponent(seleccion)}`
            })
            .then(response => response.json())
            .then(data => {
                if(data.error) {
                    console.error('Error:', data.error);
                } else {
                    console.log('Puntos sumados:', data.puntos_sumados);
                }
                avanzarPregunta();
            })
            .catch(error => {
                console.error('Error en la petición:', error);
                avanzarPregunta();
            });
        } else {
            avanzarPregunta();
        }
    }

    function avanzarPregunta() {
        indice++;
        tiempoRestante = 60;
        localStorage.setItem('quizIndice', indice);
        localStorage.setItem('quizTiempo', tiempoRestante);
        mostrarPregunta();
    }

    function siguientePregunta() {
        guardarRespuesta();
    }

    function forzarSiguiente() {
        siguientePregunta();
    }

 function mostrarFinal() {
    clearInterval(intervalo);
    localStorage.removeItem('quizIndice');
    localStorage.removeItem('quizTiempo');

    // Llamada para marcar completado
    fetch('marcar_completado.php', { method: 'POST' })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            console.log('Usuario marcado como completado');
        } else {
            console.error('Error al marcar completado:', data.error);
        }
    })
    .catch(error => {
        console.error('Error en la petición:', error);
    });

    document.querySelector(".contenedor").innerHTML = `
        <div class="final">
            <p>¡Has completado el quiz!</p>
            <p>Gracias por participar.</p>
        </div>
    `;
}


    mostrarPregunta();
</script>

</body>
</html>
