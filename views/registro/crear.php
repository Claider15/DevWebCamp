<main class="registro">
    <h2 class="registro__heading"><?php echo $titulo; ?></h2>
    <p class="registro__descripcion">Elige tu plan</p>

    <div class="paquetes__grid">
        <div class="paquete">
            <h3 class="paquete__nombre">Pase Gratis</h3>
            <ul class="paquete__lista">
                <li class="paquete__elemento">Acceso Virtual a DevWebcamp</p>
            </ul>
            <p class="paquete__precio">$0</p>

            <form method="POST" action="/finalizar-registro/gratis">
                <input class="paquetes__submit" type="submit" value="Incripcion Gratis">
            </form>
        </div>

        <div class="paquete">
            <h3 class="paquete__nombre">Pase Presencial</h3>
            <ul class="paquete__lista">
                <li class="paquete__elemento">Acceso Presencial a DevWebcamp</p>
                <li class="paquete__elemento">Pase por 2 días</p>
                <li class="paquete__elemento">Acceso a talleres y conferencias</p>
                <li class="paquete__elemento">Acceso a las Grabaciones</p>
                <li class="paquete__elemento">Camisa del Evento</p>
                <li class="paquete__elemento">Comida y Bebida</p>
            </ul>
            <p class="paquete__precio">$199</p>

            <div id="smart-button-container">
                <div style="text-align: center;">
                <div id="paypal-button-container"></div>
            </div>
</div>
        </div>

        <div class="paquete">
            <h3 class="paquete__nombre">Pase Virtual</h3>
            <ul class="paquete__lista">
                <li class="paquete__elemento">Acceso Virtual a DevWebcamp</p>
                <li class="paquete__elemento">Pase por 2 días</p>
                <li class="paquete__elemento">Enlace a talleres y conferencias</p>
                <li class="paquete__elemento">Acceso a las Grabaciones</p>
            </ul>
            <p class="paquete__precio">$49</p>

            <div id="smart-button-container">
                <div style="text-align: center;">
                <div id="paypal-button-container-virtual"></div>
            </div>
    </div>
        </div>
    </div>
</main>


  <script src="https://www.paypal.com/sdk/js?client-id=AdX-fqDqQ0VbHBLqFGlhCjvF7MFNQeasKbNNFxEMASL7s9I2rbTAQvdTJrw7rWfp4UEMPqbaiDX288-4&enable-funding=venmo&currency=USD" data-sdk-integration-source="button-factory"></script>
  <script>
    function initPayPalButton() {
      paypal.Buttons({
        style: {
          shape: 'rect',
          color: 'blue',
          layout: 'vertical',
          label: 'pay',
          
        },

        createOrder: function(data, actions) {
          return actions.order.create({
            purchase_units: [{"description":"1","amount":{"currency_code":"USD","value":230.84,"breakdown":{"item_total":{"currency_code":"USD","value":199},"shipping":{"currency_code":"USD","value":0},"tax_total":{"currency_code":"USD","value":31.84}}}}]
          });
        },

        onApprove: function(data, actions) {
          return actions.order.capture().then(function(orderData) {
            
            const datos = new FormData(); // para crear el objeto de FormData y enviar la inf.
            datos.append('paquete_id', orderData.purchase_units[0].description);
            datos.append('pago_id', orderData.purchase_units[0].payments.captures[0].id);
            
            fetch('/finalizar-registro/pagar', {
                method: 'POST', // hay que cambiar el método porque por default es GET
                body: datos
            }).then( respuesta => respuesta.json()) // retornamos la respuesta de tipo json
            .then( resultado => {
                if (resultado.resultado) {
                    actions.redirect('http://localhost:3000/finalizar-registro/conferencias')
                }
            })
          });
        },

        onError: function(err) {
          console.log(err);
        }
      }).render('#paypal-button-container');

      // Pase virtual
      paypal.Buttons({
        style: {
          shape: 'rect',
          color: 'blue',
          layout: 'vertical',
          label: 'pay',
        },

        createOrder: function(data, actions) {
          return actions.order.create({
            purchase_units: [{"description":"2","amount":{"currency_code":"USD","value":49}}]
          });
        },

        onApprove: function(data, actions) {
          return actions.order.capture().then(function(orderData) {
            const datos = new FormData(); // para crear el objeto de FormData y enviar la inf.
            datos.append('paquete_id', orderData.purchase_units[0].description);
            datos.append('pago_id', orderData.purchase_units[0].payments.captures[0].id);
            
            fetch('/finalizar-registro/pagar', {
                method: 'POST', // hay que cambiar el método porque por default es GET
                body: datos
            }).then( respuesta => respuesta.json()) // retornamos la respuesta de tipo json
            .then( resultado => {
                if (resultado.resultado) {
                    actions.redirect('http://localhost:3000/finalizar-registro/conferencias')
                }
            })
          });
        },

        onError: function(err) {
          console.log(err);
        }
      }).render('#paypal-button-container-virtual');
    }
    initPayPalButton();
  </script>