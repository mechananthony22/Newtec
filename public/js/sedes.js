 // Crear el mapa
 const map = L.map('map').setView([-6.771119806578753, -79.8453641392826], 14); // Coordenadas iniciales

 // Añadir capa base
 L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
     maxZoom: 18,
     attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
 }).addTo(map);


 // Coordenadas de las sedes
 const sedes = {
     colibri: { lat: -6.7695212965955545, lng: -79.8585781835562 },
     leguia: { lat: -6.762436121806518, lng: -79.84163160822203 },
     "pedro-ruiz": { lat: -6.768133333443712, lng: -79.83868330677457 },
 };

 // Actualizar mapa según selección
 const selectElement = document.getElementById('sede-select');
 selectElement.addEventListener('change', (event) => {
     const selected = event.target.value;

     if (selected !== "default") {
         const { lat, lng } = sedes[selected];
         map.setView([lat, lng], 16); // Centrar el mapa en la sede
         L.marker([lat, lng])
             .addTo(map)
             .bindPopup(`Sede: ${selected}`)
             .openPopup();
     }
 });