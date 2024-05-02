<?php
// Definir la URL base de la PokéAPI
$pokeApiBaseUrl = 'https://pokeapi.co/api/v2/pokemon/';

// Función para obtener datos del Pokémon por ID
function getPokemonData($pokemonId) {
    global $pokeApiBaseUrl;
    $pokemonUrl = $pokeApiBaseUrl . $pokemonId;

    // Obtener datos de la API y comprobar errores
    $pokemonJson = @file_get_contents($pokemonUrl);
    if ($pokemonJson === false) {
        error_log("No se pudo obtener datos para el Pokémon con ID $pokemonId.");
        return null;
    }

    $pokemonData = json_decode($pokemonJson);

    if ($pokemonData === null) {
        error_log("Error al decodificar datos JSON para el Pokémon con ID $pokemonId.");
        return null;
    }

    return $pokemonData;
}

// Obtener datos para los primeros 23 Pokémon
$pokeIds = range(1, 23); // Crear un rango de 1 a 23
$pokemonList = []; // Lista para almacenar datos de Pokémon

foreach ($pokeIds as $pokeId) { // Usar 'as' en lugar de 'como'
    $pokemonData = getPokemonData($pokeId); // Obtener datos del Pokémon por ID

    if ($pokemonData === null) {
        continue; // Si no se pudieron obtener datos, pasar al siguiente
    }
}

    // Intentar obtener el nombre en español
    $pokeName = 'Desconocido';
    if (property_exists($pokemonData, 'names')) {
        foreach ($pokemonData->names as $nameObj) {
            if ($nameObj->language->name == 'es') {
                $pokeName = $nameObj->name;
                break;
            }
        }
    }

    // Obtener tipos y asegurarse de que existan
$types = [];
if (property_exists($pokemonData, 'types')) {
    $types = array_map(fn($t) => $t->type->name, $pokemonData->types); // Cambiar 'son' a '='
}


    // Obtener estadísticas y asegurarse de que existan
    $stats = [];
    if (property_exists($pokemonData, 'stats')) {
        $stats = array_map(fn($s) => ['name' => $s->stat->name, 'value' => $s->base_stat], $pokemonData->stats); // Usar '=' en lugar de 'son'
    }
    

    // Obtener habilidades y asegurarse de que existan
    $abilities = [];
if (property_exists($pokemonData, 'abilities')) {
    $abilities = array_map(fn($a) => $a->ability->name, $pokemonData->abilities); // Corregir 'son' por '='
}


$imageUrl = '';
if (property_exists($pokemonData, 'sprites') && property_exists($pokemonData->sprites, 'front_default')) {
    $imageUrl = $pokemonData->sprites->front_default; // Corregir 'es' por '='
}

$pokemonList[] = [
    'id' => $pokemonData->id,
    'name' => $pokeName,
    'types' => $types, // Asegúrate de incluir '=>' para cada clave
    'stats' => $stats,
    'abilities' => $abilities,
    'image_url' => $imageUrl,
];


?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pokédex</title>
    <link rel="shortcut icon" href="src/imagens/favicon.ico" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;800&display=swap" rel="stylesheet">
    <style>
        * {
            padding: 0;
            margen 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
        }

        main {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #DADFE8;
        }

        .pokedex {
            display: flex;
            flex-direction: column;
            gap: 30px;
        }

        .cartao-pokemon {
            box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px;
            border-radius: 10px;
            display: none;
        }

        .cartao-pokemon.aberto {
            display: block;
        }

        .cartao-pokemon .cartao-topo {
            padding: 20px 30px 0;
        }

        .cartao-pokemon .cartao-imagem {
            width: 300px;
            height: 300px;
        }

        .cartao-pokemon .cartao-informacoes {
            display: flex;
            justify-content: space-between;
            background-color: #FFFFFF;
            padding: 50px 30px 30px;
            border-bottom-left-radius: 10px;
            border-bottom-right-radius: 10px;
        }

        .cartao-pokemon .cartao-informacoes .status, .cartao-pokemon .cartao-informacoes .habilidades {
            width: 180px;
        }

        .cartao-pokemon .cartao-informacoes h3 {
            font-size: 18px;
            border-bottom: 1px sólido #6B727A;
            padding-bottom: 10px;
        }

        @media (max-width: 750px) {
            .pokedex {
                flex-direction: column;
            }

            .cartao-pokemon .cartao-imagem {
                width: 250px;
                height: 250px.
            }
        }

        img {
            transition-duration: 0.3s;
            animation: slider 0.7s;
        }

        @keyframes slider {
            from {
                opacity: 0;
                transform: translateX(45px);
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
    </style>
</head>
<body>
    <main>
        <!-- Selector para elegir un Pokémon y el botón Buscar en la parte superior -->
        <nav>
            <select id="pokemon-select">
                <?php foreach ($pokemonList as $pokemon): ?>
                    <option value="cartao-<?php echo $pokemon['name']; ?>"><?php echo ucfirst($pokemon['name']); ?></option>
                <?php endforeach; ?>
            </select>
            <button id="buscar-btn">Buscar</button>
        </nav>

        <div class="pokedex">
            <div class="cartoes-pokemon">
                <?php 
                    // Mostrar solo un Pokémon al inicio
                    $primerPokemon = reset($pokemonList);
                ?>
                    <div class="cartao-pokemon tipo-<?php echo strtolower($primerPokemon['types'][0]); ?> aberto" id="cartao-<?php echo $primerPokemon['name']; ?>">
                        <div class="cartao-topo">
                            <div class="detalles">
                                <h2 class="nombre"><?php echo ucfirst($primerPokemon['name']); ?></h2>
                                <span>#<?php echo str_pad($primerPokemon['id'], 3, '0', STR_PAD_LEFT); ?></span>
                            </div>

                            <span class="tipo"><?php echo implode(', ', array_map('ucfirst', $primerPokemon['types'])); ?></span>

                            <div class="cartao-imagem">
                                <img src="<?php echo $primerPokemon['image_url']; ?>" alt="<?php echo $primerPokemon['name']; ?>">
                            </div>
                        </div>

                        <div class="cartao-informacoes">
                            <div class="status">
                                <h3>Status</h3>
                                <ul>
    <?php foreach ($primerPokemon['stats'] as $stat): ?> <!-- Corregir "como" por "as" -->
        <li><?php echo ucfirst($stat['name']); ?>: <?php echo $stat['value']; ?></li>
    <?php endforeach; ?>
</ul>

                            </div>

                            <div class="habilidades">
    <h3>Habilidades</h3>
    <ul>
        <?php foreach ($primerPokemon['abilities'] as $ability): ?> <!-- Cambiar "como" por "as" -->
            <li><?php echo ucfirst($ability); ?></li>
        <?php endforeach; ?>
    </ul>
</div>

                        </div>
                    </div>
            </div>
        </div>
    </main>

    <script>
        const buscarBtn = document.getElementById("buscar-btn");
        const pokemonSelect = document.getElementById("pokemon-select");

        buscarBtn.addEventListener("click", () => {
            // Remover la clase "aberto" del Pokémon que estaba abierto
            const cartaoPokemonAberto = document.querySelector('.aberto');
            if (cartaoPokemonAberto) {
                cartaoPokemonAberto.classList.remove('aberto');
            }

            // Obtener el Pokémon seleccionado del select
            const selectedPokemonId = pokemonSelect.value;

            const cartaoParaAbrir es documento.getElementById(selectedPokemonId);
            if (cartaoParaAbrir) {
                cartaoParaAbrir.classList.add('aberto');
            }
        });
    </script>
</body>
</html>


    <script>
        const buscarBtn = document.getElementById("buscar-btn");
        const pokemonSelect = document.getElementById("pokemon-select");
        const pokemonsCard = document.querySelectorAll('.cartao-pokemon');

        buscarBtn.addEventListener("click", () => {
            // Remover la clase "aberto" del Pokémon que estaba abierto
            const cartaoPokemonAberto = document.querySelector('.aberto');
            if (cartaoPokemonAberto) {
                cartaoPokemonAberto.classList.remove('aberto');
            }

            // Obtener el Pokémon seleccionado en el select
            const selectedPokemonId = pokemonSelect.value;

            const cartaoParaAbrir = document.getElementById(selectedPokemonId);
            if (cartaoParaAbrir) {
                cartaoParaAbrir.classList.add('aberto');
            }
        });
    </script>
</body>
</html>
