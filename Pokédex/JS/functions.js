const buscarBtn = document.getElementById("buscar-btn");
const pokemonSelector = document.getElementById("pokemon-selector");

buscarBtn.addEventListener("click", () => {
    const selectedPokemonId = pokemonSelector.value;
    const cartoesPokemon = document.querySelectorAll('.cartao-pokemon');
    cartoesPokemon.forEach(cartaoPokemon => {
        cartaoPokemon.classList.remove('aberto');
    });
    const cartaoPokemon = document.getElementById('cartao-' + selectedPokemonId);
    if (cartaoPokemon) {
        cartaoPokemon.classList.add('aberto');
    }
});
