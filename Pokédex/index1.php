<?php
// Si hay variables PHP o lógica que necesitas incluir, puedes agregarla aquí
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pokédex</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="Style/Buscador.css">
    <script src="https://kit.fontawesome.com/b408879b64.js" crossorigin="anonymous"></script>
</head>
<body>
    <!-- Selector para elegir un Pokémon y el botón Buscar en la parte superior -->
    <div class="contenedor">
    <a href="catalogoPokemon.html" class="regresar">REGRESAR</a>
        <img class="ashimg" src="img/ash.png" alt="">
        
        <button class="buscador-btn1"><i class="fa-solid fa-magnifying-glass"></i></button>
        <div class="desplazar-pokemon">
            <select id="pokemon-selector">  
            </select>
        </div>
        <div class="pokedex">
            <div class="cartoes-pokemon">
                <div class="cartao-pokemon aberto">
                    <div class="cartao-imagem">
                        <div id="pokemon-info">
                            <p><strong>Nombre:</strong> <span id="pokemon-name"></span></p>
                            <p><strong>ID:</strong> <span id="pokemon-id"></span></p>
                        </div>
                        <img id="pokemon-image" src="" alt="">
                    </div>
                    
                    
                    <div class="cartao-informacoes">
                        <div class="habilidades" id="pokemon-habilidades">
                        </div>
                        <div class="status" id="pokemon-stats">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    </div>

    <script src="JS/functions.js"></script>
    <script>
        // Función para obtener y mostrar la información de un Pokémon seleccionado
        function mostrarPokemonInfo(pokemonName) {
            fetch(`https://pokeapi.co/api/v2/pokemon/${pokemonName}`)
                .then(response => response.json())
                .then(data => {
                    const pokemonImage = document.getElementById('pokemon-image');
                    const pokemonInfo = document.getElementById('pokemon-info');
                    const pokemonHabilidades = document.getElementById('pokemon-habilidades');
                    const pokemonStats = document.getElementById('pokemon-stats');

                    // Mostrar la imagen del Pokémon (official artwork) junto con el nombre y el ID
                    const pokemonID = data.id;
                    const officialArtworkURL = `https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/${pokemonID}.png`;
                    pokemonImage.src = officialArtworkURL;
                    pokemonImage.alt = pokemonName;
                    pokemonInfo.innerHTML = `<p><strong>Nombre:</strong> ${data.name.charAt(0).toUpperCase() + data.name.slice(1)}</p><p><strong>ID:</strong> ${pokemonID}</p>`;

                    // Mostrar las habilidades del Pokémon en español
                    pokemonHabilidades.innerHTML = "<h3>Habilidades</h3>";
                    data.abilities.forEach(ability => {
                        const abilityName = ability.ability.name.replace('-', ' '); // Reemplazar guiones con espacios
                        pokemonHabilidades.innerHTML += `<p>${abilityName.charAt(0).toUpperCase() + abilityName.slice(1)}</p>`;
                    });

                    // Mostrar las estadísticas del Pokémon en español
                    pokemonStats.innerHTML = "<h3>Estadísticas</h3>";
                    data.stats.forEach(stat => {
                        const statName = {
                            hp: "PS",
                            attack: "Ataque",
                            defense: "Defensa",
                            "special-attack": "Ataque Especial",
                            "special-defense": "Defensa Especial",
                            speed: "Velocidad"
                        };
                        pokemonStats.innerHTML += `<p>${statName[stat.stat.name]}: ${stat.base_stat}</p>`;
                    });
                })
                .catch(error => console.log('Hubo un error al obtener los datos del Pokémon:', error));
        }

        // Obtener referencia al select de Pokémon
        const pokemonSelect = document.getElementById('pokemon-selector');

        // Llenar el select con opciones de Pokémon
        fetch('https://pokeapi.co/api/v2/pokemon?limit=151') // Obtener los primeros 151 Pokémon
            .then(response => response.json())
            .then(data => {
                data.results.forEach(pokemon => {
                    const option = document.createElement('option');
                    option.value = pokemon.name;
                    option.textContent = pokemon.name.charAt(0).toUpperCase() + pokemon.name.slice(1);
                    pokemonSelect.appendChild(option);
                });
            })
            .catch(error => console.log('Hubo un error al obtener la lista de Pokémon:', error));

        // Añadir evento de cambio al select para mostrar la información del Pokémon seleccionado
        pokemonSelect.addEventListener('change', function() {
            const selectedPokemon = this.value;
            mostrarPokemonInfo(selectedPokemon);
        });
    </script>
</body>
</html>

