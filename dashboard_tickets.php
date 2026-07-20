<?php
session_start();
if (!isset($_SESSION['cedula'])) {
    header("Location: index.html");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Tickets - Centro de Soporte</title>
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/bootstrap-icons/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/nueva-identidad.css">
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        :root {
            --primary-bg: #f8f9fa;
            --card-bg: #ffffff;
            --text-main: #333333;
            --text-muted: #6c757d;
        }

        body {
            background-color: var(--primary-bg);
            color: var(--text-main);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .ticket-card {
            background: var(--card-bg);
            border-radius: 12px;
            border: none;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05), 0 1px 3px rgba(0,0,0,0.1);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            margin-bottom: 1.5rem;
        }

        .ticket-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.1);
        }

        .rating-star {
            font-size: 2.5rem;
            color: #e4e5e9;
            cursor: pointer;
            transition: color 0.2s ease, transform 0.2s ease;
            display: inline-block;
        }
        
        .rating-star:hover {
            transform: scale(1.1);
        }

        .rating-star.active,
        .rating-star.hover {
            color: #ffc107;
        }


        /* Accesibilidad - focus visible */
        button:focus-visible, a:focus-visible, .rating-star:focus-visible {
            outline: 3px solid var(--bs-primary);
            outline-offset: 2px;
        }
        
        [x-cloak] { display: none !important; }
    </style>
</head>
<body>
    <?php include 'partials/header.php'; ?>

    <main class="container py-5" x-data="ticketsDashboard()" x-init="cargarTickets()">
        
        <header class="d-flex justify-content-between align-items-center mb-4">
            <a href="plataforma_soporte.php" class="btn btn-outline-secondary" aria-label="Volver al menú principal">
                <i class="bi bi-arrow-left me-2" aria-hidden="true"></i>Volver
            </a>
            <h1 class="h3 mb-0 fw-bold">Mis Tickets</h1>
            <span class="badge bg-primary fs-6 shadow-sm" aria-label="Cantidad de tickets activos">
                <i class="bi bi-ticket-perforated me-1" aria-hidden="true"></i> 
                <span x-text="tickets.length"></span> Activos
            </span>
        </header>

        <!-- Skeleton loader mientras carga -->
        <div x-show="cargando" class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando tickets...</span>
            </div>
            <p class="mt-2 text-muted">Obteniendo información...</p>
        </div>

        <div x-show="!cargando" x-cloak>
            
            <!-- Sección de tickets pendientes por valorar (Destacados) -->
            <section aria-labelledby="titulo-valorar" x-show="paraValorar.length > 0">
                <h2 id="titulo-valorar" class="h4 mb-3 text-success fw-bold">
                    <i class="bi bi-star-fill me-2" aria-hidden="true"></i>Pendientes de Valorar
                </h2>
                
                <div class="row">
                    <template x-for="ticket in paraValorar" :key="ticket.id">
                        <article class="col-md-6">
                            <!-- Diseño 60-30-10: Fondo blanco, acentos verde success -->
                            <div class="ticket-card p-4 border-success border-top border-4">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h3 class="h5 text-success mb-0 fw-bold" x-text="ticket.codigo_tkt"></h3>
                                    <span class="badge bg-success" x-text="ticket.estado_nombre"></span>
                                </div>
                                <p class="mb-3 text-truncate text-muted" x-text="ticket.asunto" :title="ticket.asunto"></p>
                                
                                <div class="bg-light p-3 rounded text-center">
                                    <p class="mb-2 fw-bold text-dark">¿Cómo califica la atención recibida?</p>
                                    
                                    <!-- Sistema de Estrellas (Accesible por teclado) -->
                                    <div class="d-flex justify-content-center gap-2 mb-2" role="radiogroup" aria-label="Calificación de 1 a 5 estrellas">
                                        <template x-for="star in 5">
                                            <i class="bi bi-star-fill rating-star" 
                                               role="radio" 
                                               :aria-checked="valoraciones[ticket.id]?.puntuacion >= star"
                                               :aria-label="star + ' estrellas'"
                                               tabindex="0"
                                               @click="setRating(ticket.id, star)"
                                               @mouseenter="hoverRating[ticket.id] = star"
                                               @mouseleave="hoverRating[ticket.id] = 0"
                                               @keydown.enter="setRating(ticket.id, star)"
                                               @keydown.space.prevent="setRating(ticket.id, star)"
                                               :class="{
                                                   'active': (valoraciones[ticket.id]?.puntuacion || 0) >= star,
                                                   'hover': (hoverRating[ticket.id] || 0) >= star
                                               }"></i>
                                        </template>
                                    </div>
                                    <div class="small text-muted mb-3" aria-live="polite">
                                        <strong x-show="valoraciones[ticket.id]?.puntuacion" x-text="getRatingText(valoraciones[ticket.id].puntuacion)" class="text-warning"></strong>
                                        <span x-show="!valoraciones[ticket.id]?.puntuacion">Seleccione una calificación</span>
                                    </div>

                                    <div x-show="valoraciones[ticket.id]?.puntuacion > 0" x-transition>
                                        <textarea class="form-control mb-3 shadow-sm border-0" rows="2" 
                                                  placeholder="Comentario adicional (opcional)..." 
                                                  x-model="valoraciones[ticket.id].comentario"
                                                  aria-label="Comentario adicional opcional"></textarea>
                                        <button class="btn btn-success w-100 shadow-sm fw-bold" 
                                                @click="enviarValoracion(ticket.id)"
                                                :disabled="enviando == ticket.id">
                                            <span x-show="enviando != ticket.id"><i class="bi bi-send-check me-2" aria-hidden="true"></i>Enviar Valoración</span>
                                            <span x-show="enviando == ticket.id" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </article>
                    </template>
                </div>
            </section>

            <!-- Separador visual -->
            <hr class="my-5 opacity-25" x-show="paraValorar.length > 0 && tickets.length > 0">

            <!-- Sección de tickets activos -->
            <section aria-labelledby="titulo-activos">
                <h2 id="titulo-activos" class="h4 mb-4 fw-bold text-dark">Tickets Activos</h2>
                
                <div x-show="tickets.length === 0 && paraValorar.length === 0" class="text-center py-5">
                    <i class="bi bi-inbox text-muted" style="font-size: 4rem;" aria-hidden="true"></i>
                    <p class="mt-3 text-muted fs-5">No tienes tickets activos en este momento.</p>
                </div>

                <div class="row">
                    <template x-for="ticket in tickets" :key="ticket.id">
                        <article class="col-12">
                            <div class="ticket-card p-4">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <div class="d-flex align-items-center gap-3 mb-3">
                                            <h3 class="h5 mb-0 fw-bold text-primary" x-text="ticket.codigo_tkt"></h3>
                                            <span class="badge text-white px-3 py-2 shadow-sm" 
                                                  :style="`background-color: ${ticket.estado_color}`" 
                                                  x-text="ticket.estado_nombre"
                                                  :aria-label="`Estado: ${ticket.estado_nombre}`"></span>
                                            <span class="badge bg-light text-dark border px-2 py-1">
                                                <i class="bi" :class="ticket.tipo == 1 ? 'bi-tools' : 'bi-briefcase-fill'" aria-hidden="true"></i> 
                                                <span x-text="ticket.tipo_nombre"></span>
                                            </span>
                                        </div>
                                        <h4 class="h6 fw-bold text-dark mb-2" x-text="ticket.asunto"></h4>
                                        <p class="mb-3 text-muted" x-text="ticket.descripcion"></p>
                                    </div>
                                    <!-- Línea de tiempo (Timeline) -->
                                    <div class="col-md-4 border-start ps-4">
                                        <h5 class="h6 mb-3 fw-bold text-secondary"><i class="bi bi-info-circle me-2" aria-hidden="true"></i>Información</h5>
                                        <div class="small">

                                            <div class="mb-3">
                                                <div class="fw-bold text-secondary">
                                                    Fecha de creación
                                                </div>
                                                <div class="text-muted"
                                                     x-text="formatearFecha(ticket.fecha_creacion)">
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <div class="fw-bold text-secondary">
                                                    Última actualización
                                                </div>
                                                <div class="text-muted"
                                                     x-text="formatearFecha(ticket.ultima_actualizacion)">
                                                </div>
                                            </div>

                                            <div>
                                                <div class="fw-bold text-secondary">
                                                    Estado actual
                                                </div>

                                                <span class="badge text-white"
                                                      :style="`background-color: ${ticket.estado_color}`"
                                                      x-text="ticket.estado_nombre">
                                                </span>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </article>
                    </template>
                </div>
            </section>

        </div>
    </main>

    <?php include 'partials/footer.php'; ?>

    <script>
        function ticketsDashboard() {
            return {
                tickets: [],
                paraValorar: [],
                cargando: true,
                enviando: null,
                hoverRating: {},
                valoraciones: {},

                async cargarTickets() {
                    try {
                        const res = await fetch('ajax/tickets_listar.php');
                        const data = await res.json();
                        
                        if (data.success) {
                            this.tickets = data.tickets;
                            this.paraValorar = data.para_valorar;
                            
                            // Inicializar objetos de valoración
                            this.paraValorar.forEach(t => {
                                this.valoraciones[t.id] = { puntuacion: 0, comentario: '' };
                            });
                        } else {
                            alert(data.message || 'Error al cargar tickets');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('Error de conexión');
                    } finally {
                        this.cargando = false;
                    }
                },

                setRating(ticketId, stars) {
                    if (!this.valoraciones[ticketId]) {
                        this.valoraciones[ticketId] = { puntuacion: 0, comentario: '' };
                    }
                    this.valoraciones[ticketId].puntuacion = stars;
                },

                getRatingText(stars) {
                    const texts = {
                        1: '1/5 - Malo',
                        2: '2/5 - Regular',
                        3: '3/5 - Bueno',
                        4: '4/5 - Muy bueno',
                        5: '5/5 - Excelente'
                    };
                    return texts[stars] || '';
                },

                formatearFecha(fechaStr) {
                    if (!fechaStr) return '';
                    const fecha = new Date(fechaStr);
                    return fecha.toLocaleDateString('es-CR', {
                        day: '2-digit', month: 'short', year: 'numeric',
                        hour: '2-digit', minute:'2-digit'
                    });
                },

                async enviarValoracion(ticketId) {
                    const val = this.valoraciones[ticketId];
                    if (!val || val.puntuacion < 1) return;

                    this.enviando = ticketId;

                    try {
                        const res = await fetch('ajax/tickets_valorar.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({
                                ticket_id: ticketId,
                                puntuacion: val.puntuacion,
                                comentario: val.comentario
                            })
                        });
                        
                        const data = await res.json();
                        
                        if (data.success) {
                            // Remover de la lista sin recargar la página
                            this.paraValorar = this.paraValorar.filter(t => t.id !== ticketId);
                        } else {
                            alert(data.message || 'Error al guardar la valoración');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('Error de conexión');
                    } finally {
                        this.enviando = null;
                    }
                }
            }
        }
    </script>
</body>
</html>
