// Función para convertir la clave pública VAPID a un formato Uint8Array
function urlBase64ToUint8Array(base64String) {
    const padding = '='.repeat((4 - base64String.length % 4) % 4); // Añadir relleno si es necesario
    const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/'); // Convertir a base64 estándar
    const rawData = window.atob(base64); // Decodificar la cadena base64 a datos binarios
    const outputArray = new Uint8Array(rawData.length); // Crear un array de bytes
    for (let i = 0; i < rawData.length; ++i) {
        outputArray[i] = rawData.charCodeAt(i); // Llenar el array con los valores binarios
    }
    return outputArray; // Devolver el Uint8Array
}
