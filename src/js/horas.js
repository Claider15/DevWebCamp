(function() {
    const horas = document.querySelector('#horas');
    
    if (horas) {
        
        const categoria = document.querySelector('[name="categoria_id"]');
        const dias = document.querySelectorAll('[name="dia"]');
        const inputHiddenDia = document.querySelector('[name="dia_id"]');
        const inputHiddenHora = document.querySelector('[name="hora_id"]');
        
        categoria.addEventListener('change', terminoBusqueda);
        dias.forEach(dia => dia.addEventListener('change', terminoBusqueda));
        
        let busqueda = {
            categoria_id: +categoria.value || '',
            dia: +inputHiddenDia.value || ''
        }

        if(!Object.values(busqueda).includes('')) { // solo se comunica con la API hasta que se llene busqueda
            (async () => {
                await buscarEventos();
    
                // Resaltar la hora actual
                const id = inputHiddenHora.value;
                const horaSeleccionada = document.querySelector(`[data-hora-id="${id}"]`);
                horaSeleccionada.classList.remove('horas__hora--deshabilitada');
                horaSeleccionada.classList.add('horas__hora--seleccionada');
                horaSeleccionada.classList.add('horas__hora--activa');
                
                horaSeleccionada.onclick = seleccionarHora;
                
            })()
            
        }

        function terminoBusqueda(e) {
            busqueda[e.target.name] = e.target.value;

            // Reiniciar los campos ocultos y el selector de horas
            inputHiddenHora.value = '';
            inputHiddenDia.value = '';
            const horaPrevia = document.querySelector('.horas__hora--seleccionada');
            if (horaPrevia) {
                horaPrevia.classList.remove('horas__hora--seleccionada');
            }

            if(Object.values(busqueda).includes('')) { // solo se comunica con la API hasta que se llene busqueda
                return;
            }
            
            buscarEventos();
        }
        
        async function buscarEventos() {
            const {dia, categoria_id} = busqueda;
            const url = `/api/eventos-horarios?dia_id=${dia}&categoria_id=${categoria_id}`;

            const resultado = await fetch(url); // el primer await hace la consulta hacia la página para ver si se puede conectar correctamente
            const eventos = await resultado.json(); // este va a ser el resultado de la consulta a la fetch API
            // Una vez que tengamos algunos eventos en la BD, van a aparecer los eventos que sean de conferencias del día sábado (ejemplo) en lugar de un arreglo vacío
            
            
            obtenerHorasDisponibles(eventos);
        }

        function obtenerHorasDisponibles(eventos) {
            // Reiniciar las horas
            const listadoHoras = document.querySelectorAll('#horas li');
            listadoHoras.forEach(li => li.classList.add('horas__hora--deshabilitada'));
            listadoHoras.forEach(li => li.classList.remove('horas__hora--activa'));


            // comprobar los eventos ya tomados y quitar la variable de deshabilitado
            const horasTomadas = eventos.map(evento => evento.hora_id);

            const listadoHorasArray = Array.from(listadoHoras);

            const resultados = listadoHorasArray.filter(li => !horasTomadas.includes(li.dataset.horaId));
            resultados.forEach(resultado => resultado.classList.remove('horas__hora--deshabilitada'));
            resultados.forEach(resultado => resultado.classList.add('horas__hora--activa'));

            const horasDisponibles = document.querySelectorAll('#horas li:not(.horas__hora--deshabilitada)');
            horasDisponibles.forEach(hora => hora.addEventListener('click', seleccionarHora));
        }
        
        function seleccionarHora(e) { // va a tomar el evento porque tenemos que identificar a qué le estamos dando click
            
            // Deshabilitar la hora previa, si hay un nuevo click
            const horaPrevia = document.querySelector('.horas__hora--seleccionada');
            if (horaPrevia) {
                horaPrevia.classList.remove('horas__hora--seleccionada');
            }
            
            
            // Agregar una clase de seleccionado
            e.target.classList.add('horas__hora--seleccionada');
            
            inputHiddenHora.value = e.target.dataset.horaId

            // Llenar el campo oculto de día
            inputHiddenDia.value = document.querySelector('[name="dia"]:checked').value;
        }
    }
})();