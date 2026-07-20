<?php
// === BLOQUEAR ACCESO DIRECTO =====
if (!defined('ACCESO_SEGURO')) {
    http_response_code(403);
    exit("Acceso directo no permitido");
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <title>Centro de Ayuda - TecnoPresta</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="bootstrap5/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/nueva-identidad.css">
  <link rel="stylesheet" href="css/formulario_menu_principal.css" />
  <link href="css/bootstrap-icons/bootstrap-icons.min.css" rel="stylesheet">
  <script src="js/jquery-3.7.1.min.js"></script>
  <script src="bootstrap5/js/bootstrap.bundle.min.js"></script>
  <style>
    :root {
      --mep-primary: #192952;
      --mep-secondary: #0035A0;
      --mep-accent: #CFAC65;
      --mep-gold: #C8A951;
      --mep-blue: #003876;
      --mep-blue2: #114c91;
      --mep-text: #2C3E50;
    }

    html { scroll-behavior: smooth; }

    /* ── Hero ── */
    .help-hero {
      background: linear-gradient(135deg, var(--mep-blue) 0%, var(--mep-blue2) 100%);
      color: #fff;
      border-radius: 24px;
      padding: 48px 40px;
      margin-top: 28px;
      margin-bottom: 36px;
      box-shadow: 0 20px 50px rgba(0,0,0,0.12);
      position: relative;
      overflow: hidden;
    }
    .help-hero::after {
      content: '';
      position: absolute;
      top: -40%;
      right: -10%;
      width: 420px;
      height: 420px;
      border-radius: 50%;
      background: rgba(255,255,255,0.04);
    }
    .help-hero h1 {
      font-weight: 700;
      font-size: 2.2rem;
      margin-bottom: 8px;
      position: relative;
      z-index: 1;
    }
    .help-hero p {
      font-size: 1.05rem;
      opacity: 0.85;
      margin: 0;
      position: relative;
      z-index: 1;
    }
    .help-hero .hero-icon {
      width: 72px;
      height: 72px;
      border-radius: 20px;
      background: rgba(255,255,255,0.14);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 32px;
      backdrop-filter: blur(8px);
      margin-bottom: 8px;
    }

    /* ── Navigation ── */
    .help-nav {
      position: sticky;
      top: 0;
      z-index: 1020;
      background: rgba(255,255,255,0.92);
      backdrop-filter: blur(14px);
      -webkit-backdrop-filter: blur(14px);
      border-radius: 16px;
      padding: 10px 18px;
      margin-bottom: 36px;
      box-shadow: 0 4px 24px rgba(0,0,0,0.06);
      display: flex;
      flex-wrap: wrap;
      gap: 6px;
      justify-content: center;
    }
    .help-nav .nav-link {
      font-size: 0.85rem;
      font-weight: 500;
      color: var(--mep-text);
      padding: 8px 18px;
      border-radius: 30px;
      transition: all 0.25s ease;
      white-space: nowrap;
    }
    .help-nav .nav-link:hover {
      background: rgba(0,56,118,0.06);
      color: var(--mep-blue);
    }
    .help-nav .nav-link.active {
      background: var(--mep-blue) !important;
      color: #fff !important;
      box-shadow: 0 3px 10px rgba(0,56,118,0.25);
    }

    /* ── Section ── */
    .help-section {
      margin-bottom: 52px;
      scroll-margin-top: 90px;
    }
    .help-section:last-child {
      margin-bottom: 0;
    }

    .section-header {
      display: flex;
      align-items: center;
      gap: 16px;
      margin-bottom: 24px;
      padding-bottom: 14px;
      border-bottom: 2px solid var(--mep-gold);
    }
    .section-header h3 {
      font-weight: 600;
      color: var(--mep-primary);
      margin: 0;
      font-size: 1.35rem;
    }
    .section-header .badge-count {
      background: var(--mep-gold);
      color: #fff;
      font-weight: 500;
      font-size: 0.7rem;
      padding: 5px 12px;
      border-radius: 20px;
      letter-spacing: 0.3px;
    }

    /* ── Video Card ── */
    .video-card {
      border: none;
      border-radius: 14px;
      overflow: hidden;
      background: #fff;
      box-shadow: 0 4px 16px rgba(0,0,0,0.05);
      transition: all 0.3s ease;
      height: 100%;
    }
    .video-card:hover {
      transform: translateY(-6px);
      box-shadow: 0 14px 36px rgba(0,56,118,0.12);
      border-bottom: 3px solid var(--mep-gold);
    }
    .video-card .ratio {
      background: #0a0a0a;
    }
    .video-card .card-body {
      padding: 14px 18px;
      background: linear-gradient(135deg, #f0f4fa 0%, #ffffff 100%);
      color: var(--mep-text);
      border-top: 2px solid var(--mep-gold);
    }
    .video-card .card-text {
      font-size: 0.88rem;
      font-weight: 500;
      color: var(--mep-text);
      margin: 0;
      line-height: 1.35;
    }

    /* ── Back to top ── */
    .btn-to-top {
      position: fixed;
      bottom: 30px;
      right: 30px;
      width: 56px;
      height: 56px;
      border-radius: 50%;
      background: var(--mep-primary);
      color: #fff;
      border: none;
      font-size: 22px;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 4px 16px rgba(0,0,0,0.2);
      transition: all 0.3s ease;
      z-index: 1030;
      cursor: pointer;
      text-decoration: none;
      opacity: 0;
      visibility: hidden;
      transform: translateY(12px);
    }
    .btn-to-top.show {
      opacity: 1;
      visibility: visible;
      transform: translateY(0);
    }
    .btn-to-top:hover {
      transform: scale(1.1) translateY(-2px);
      background: var(--mep-secondary);
      color: #fff;
    }
    /* ── Subsistemas ── */
    .btn-to-subsistemas {
      position: fixed;
      bottom: 96px;
      right: 30px;
      width: 56px;
      height: 56px;
      border-radius: 50%;
      background: var(--mep-gold);
      color: #fff;
      border: none;
      font-size: 24px;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 4px 16px rgba(0,0,0,0.2);
      transition: all 0.3s ease;
      z-index: 1030;
      cursor: pointer;
      text-decoration: none;
    }
    .btn-to-subsistemas:hover {
      transform: scale(1.1);
      background: var(--mep-primary);
      color: #fff;
    }
    .btn-to-subsistemas::before {
      content: "Volver a Menú Principal";
      position: absolute;
      right: 64px;
      background: rgba(0,0,0,0.8);
      color: #fff;
      padding: 6px 12px;
      border-radius: 8px;
      font-size: 0.75rem;
      white-space: nowrap;
      opacity: 0;
      pointer-events: none;
      transition: opacity 0.3s;
      font-family: 'Henderson Sans', Arial, sans-serif;
    }
    .btn-to-subsistemas:hover::before {
      opacity: 1;
    }

    .btn-to-top::before {
      content: "Volver arriba";
      position: absolute;
      right: 64px;
      background: rgba(0,0,0,0.8);
      color: #fff;
      padding: 6px 12px;
      border-radius: 8px;
      font-size: 0.75rem;
      white-space: nowrap;
      opacity: 0;
      pointer-events: none;
      transition: opacity 0.3s;
      font-family: 'Henderson Sans', Arial, sans-serif;
    }
    .btn-to-top:hover::before {
      opacity: 1;
    }

    /* ── Responsive ── */
    @media (max-width: 768px) {
      .help-hero { padding: 32px 20px; }
      .help-hero h1 { font-size: 1.6rem; }
      .help-nav { padding: 8px 12px; gap: 4px; }
      .help-nav .nav-link { font-size: 0.78rem; padding: 6px 12px; }
      .btn-to-subsistemas { width: 48px; height: 48px; font-size: 20px; bottom: 78px; right: 20px; }
      .btn-to-subsistemas::before { display: none; }
      .btn-to-top { width: 48px; height: 48px; font-size: 18px; bottom: 20px; right: 20px; }
      .btn-to-top::before { display: none; }
    }

    /* ── Section divider ── */
    .section-divider {
      height: 1px;
      background: linear-gradient(to right, transparent, var(--mep-gold), transparent);
      margin: 0 0 40px 0;
      opacity: 0.4;
    }

    .help-footer-spacer {
      height: 40px;
    }
  </style>
</head>
<body class="layout-page">
    <?php include 'partials/header.php'; ?>

    <div class="container contenido-principal py-3">

        <!-- ══════ HERO ══════ -->
        <div class="help-hero d-flex align-items-center gap-4">
            <div class="hero-icon flex-shrink-0">
                <i class="bi bi-question-circle-fill"></i>
            </div>
            <div>
                <h1>Centro de Ayuda</h1>
                <p>Gu&iacuteas en video para aprovechar TecnoPresta al m&aacuteximo</p>
            </div>
        </div>

        <!-- ══════ NAVEGACIÓN ══════ -->
        <nav class="help-nav" id="helpNav">
            <a class="nav-link" href="#s0"><i class="bi bi-info-circle me-1"></i>General</a>
            <a class="nav-link" href="#s1"><i class="bi bi-box-seam me-1"></i>Activos</a>
            <a class="nav-link" href="#s2"><i class="bi bi-cpu me-1"></i>Software</a>
            <a class="nav-link" href="#s3"><i class="bi bi-tools me-1"></i>Mantenimiento</a>
            <a class="nav-link" href="#s4"><i class="bi bi-file-earmark-bar-graph me-1"></i>Reportes</a>
            <a class="nav-link" href="#s5"><i class="bi bi-hand-index-thumb me-1"></i>Pr&eacutestamo</a>
        </nav>

        <!-- ══════ SECCIÓN 0 — TECNOPRESTA ══════ -->
        <section id="s0" class="help-section">
            <div class="section-header">
                <h3><i class="bi bi-info-circle-fill me-2" style="color:var(--mep-gold);"></i>TecnoPresta</h3>
                <span class="badge-count">3 videos</span>
            </div>
            <div class="row g-4 row-cols-1 row-cols-md-2 row-cols-lg-3">
                <!-- Tour -->
                <div class="col" id="tour">
                    <div class="card video-card">
                        <div class="ratio ratio-16x9">
                            <iframe src="https://www.youtube.com/embed/IAX6ggvVxUs" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                        </div>
                        <div class="card-body">
                            <p class="card-text"><i class="bi bi-play-circle-fill me-1" style="color:var(--mep-gold);"></i>Tours por TecnoPresta</p>
                        </div>
                    </div>
                </div>
                <!-- Ingresar -->
                <div class="col">
                    <div class="card video-card">
                        <div class="ratio ratio-16x9">
                            <iframe src="https://www.youtube.com/embed/nv4B79L2zsM" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                        </div>
                        <div class="card-body">
                            <p class="card-text"><i class="bi bi-play-circle-fill me-1" style="color:var(--mep-gold);"></i>Ingresar al Sistema</p>
                        </div>
                    </div>
                </div>
                <!-- Cerrar -->
                <div class="col">
                    <div class="card video-card">
                        <div class="ratio ratio-16x9">
                            <iframe src="https://www.youtube.com/embed/nv4B79L2zsM" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                        </div>
                        <div class="card-body">
                            <p class="card-text"><i class="bi bi-play-circle-fill me-1" style="color:var(--mep-gold);"></i>Cerrar Sistema</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="section-divider"></div>

        <!-- ══════ SECCIÓN 1 — INVENTARIO ACTIVOS ══════ -->
        <section id="s1" class="help-section">
            <div class="section-header">
                <h3><i class="bi bi-box-seam-fill me-2" style="color:var(--mep-gold);"></i>Inventario Activos</h3>
                <span class="badge-count">12 videos</span>
            </div>
            <div class="row g-4 row-cols-1 row-cols-md-2 row-cols-lg-3">
                <div class="col">
                    <div class="card video-card">
                        <div class="ratio ratio-16x9">
                            <iframe src="https://www.youtube.com/embed/I40ZYeaV6UU" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                        </div>
                        <div class="card-body">
                            <p class="card-text"><i class="bi bi-play-circle-fill me-1" style="color:var(--mep-gold);"></i>Paseo General por el M&oacutedulo</p>
                        </div>
                    </div>
                </div>
                <div class="col" id="mg">
                    <div class="card video-card">
                        <div class="ratio ratio-16x9">
                            <iframe src="https://www.youtube.com/embed/KVjv4hanaMk" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                        </div>
                        <div class="card-body">
                            <p class="card-text"><i class="bi bi-play-circle-fill me-1" style="color:var(--mep-gold);"></i>Agregar Placa del Activo [Modelo de Activo]</p>
                        </div>
                    </div>
                </div>
                <div class="col" id="sp">
                    <div class="card video-card">
                        <div class="ratio ratio-16x9">
                            <iframe src="https://www.youtube.com/embed/y_SHK9UuZlo" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                        </div>
                        <div class="card-body">
                            <p class="card-text"><i class="bi bi-play-circle-fill me-1" style="color:var(--mep-gold);"></i>Agregar Placa y Serial</p>
                        </div>
                    </div>
                </div>
                <div class="col" id="lt">
                    <div class="card video-card">
                        <div class="ratio ratio-16x9">
                            <iframe src="https://www.youtube.com/embed/f-f0AkBeGvc" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                        </div>
                        <div class="card-body">
                            <p class="card-text"><i class="bi bi-play-circle-fill me-1" style="color:var(--mep-gold);"></i>2) Importar Placas y Seriales por Lotes</p>
                        </div>
                    </div>
                </div>
                <div class="col" id="pa">
                    <div class="card video-card">
                        <div class="ratio ratio-16x9">
                            <iframe src="https://www.youtube.com/embed/DRhzasJ83T8" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                        </div>
                        <div class="card-body">
                            <p class="card-text"><i class="bi bi-play-circle-fill me-1" style="color:var(--mep-gold);"></i>3) Pasar Activos entre Instancias</p>
                        </div>
                    </div>
                </div>
                <div class="col" id="ea">
                    <div class="card video-card">
                        <div class="ratio ratio-16x9">
                            <iframe src="https://www.youtube.com/embed/QFbp45FAncs" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                        </div>
                        <div class="card-body">
                            <p class="card-text"><i class="bi bi-play-circle-fill me-1" style="color:var(--mep-gold);"></i>4) Estado de los Activos</p>
                        </div>
                    </div>
                </div>
                <div class="col" id="db">
                    <div class="card video-card">
                        <div class="ratio ratio-16x9">
                            <iframe src="https://www.youtube.com/embed/WSPz_tTmjDU" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                        </div>
                        <div class="card-body">
                            <p class="card-text"><i class="bi bi-play-circle-fill me-1" style="color:var(--mep-gold);"></i>5) Sacar de Inventario</p>
                        </div>
                    </div>
                </div>
                <div class="col" id="adp">
                    <div class="card video-card">
                        <div class="ratio ratio-16x9">
                            <iframe src="https://www.youtube.com/embed/7BtUyGGSFGE" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                        </div>
                        <div class="card-body">
                            <p class="card-text"><i class="bi bi-play-circle-fill me-1" style="color:var(--mep-gold);"></i>6) Activos Destinados a Prestarse</p>
                        </div>
                    </div>
                </div>
                <div class="col" id="aan">
                    <div class="card video-card">
                        <div class="ratio ratio-16x9">
                            <iframe src="https://www.youtube.com/embed/m4CytOP0234" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                        </div>
                        <div class="card-body">
                            <p class="card-text"><i class="bi bi-play-circle-fill me-1" style="color:var(--mep-gold);"></i>7) Asignar Alias y Numeraci&oacuten a los Activos</p>
                        </div>
                    </div>
                </div>
                <div class="col" id="au">
                    <div class="card video-card">
                        <div class="ratio ratio-16x9">
                            <iframe src="https://www.youtube.com/embed/e2a1u8Ju05M" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                        </div>
                        <div class="card-body">
                            <p class="card-text"><i class="bi bi-play-circle-fill me-1" style="color:var(--mep-gold);"></i>8) Ubicaci&oacuten del Activo</p>
                        </div>
                    </div>
                </div>
                <div class="col" id="ce">
                    <div class="card video-card">
                        <div class="ratio ratio-16x9">
                            <iframe src="https://www.youtube.com/embed/4H-lJgpdtrc" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                        </div>
                        <div class="card-body">
                            <p class="card-text"><i class="bi bi-play-circle-fill me-1" style="color:var(--mep-gold);"></i>9) Agregar Campus / Edificio</p>
                        </div>
                    </div>
                </div>
                <div class="col" id="el">
                    <div class="card video-card">
                        <div class="ratio ratio-16x9">
                            <iframe src="https://www.youtube.com/embed/ud2H9fZWTCA" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                        </div>
                        <div class="card-body">
                            <p class="card-text"><i class="bi bi-play-circle-fill me-1" style="color:var(--mep-gold);"></i>10) Agregar Estancia / Lugar</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="section-divider"></div>

        <!-- ══════ SECCIÓN 2 — SOFTWARE ══════ -->
        <section id="s2" class="help-section">
            <div class="section-header">
                <h3><i class="bi bi-cpu-fill me-2" style="color:var(--mep-gold);"></i>Inventario de Software</h3>
                <span class="badge-count">3 videos</span>
            </div>
            <div class="row g-4 row-cols-1 row-cols-md-2 row-cols-lg-3">
                <div class="col" id="s">
                    <div class="card video-card">
                        <div class="ratio ratio-16x9">
                            <iframe src="https://www.youtube.com/embed/_XS0Hmb6Z-c" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                        </div>
                        <div class="card-body">
                            <p class="card-text"><i class="bi bi-play-circle-fill me-1" style="color:var(--mep-gold);"></i>1) Agregar Licencia</p>
                        </div>
                    </div>
                </div>
                <div class="col" id="ale">
                    <div class="card video-card">
                        <div class="ratio ratio-16x9">
                            <iframe src="https://www.youtube.com/embed/xtiNFbxXL5k" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                        </div>
                        <div class="card-body">
                            <p class="card-text"><i class="bi bi-play-circle-fill me-1" style="color:var(--mep-gold);"></i>2) Ligar Licencias al PC</p>
                        </div>
                    </div>
                </div>
                <div class="col" id="dle">
                    <div class="card video-card">
                        <div class="ratio ratio-16x9">
                            <iframe src="https://www.youtube.com/embed/qySW9UOYK2Q" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                        </div>
                        <div class="card-body">
                            <p class="card-text"><i class="bi bi-play-circle-fill me-1" style="color:var(--mep-gold);"></i>3) Desligar Licencias del PC</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="section-divider"></div>

        <!-- ══════ SECCIÓN 3 — MANTENIMIENTO ══════ -->
        <section id="s3" class="help-section">
            <div class="section-header">
                <h3><i class="bi bi-tools me-2" style="color:var(--mep-gold);"></i>Mantenimiento</h3>
                <span class="badge-count">11 videos</span>
            </div>
            <div class="row g-4 row-cols-1 row-cols-md-2 row-cols-lg-3">
                <div class="col" id="ag">
                    <div class="card video-card">
                        <div class="ratio ratio-16x9">
                            <iframe src="https://www.youtube.com/embed/f9EfqvMeXvQ" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                        </div>
                        <div class="card-body">
                            <p class="card-text"><i class="bi bi-play-circle-fill me-1" style="color:var(--mep-gold);"></i>1) Agregar Activo General</p>
                        </div>
                    </div>
                </div>
                <div class="col" id="mc">
                    <div class="card video-card">
                        <div class="ratio ratio-16x9">
                            <iframe src="https://www.youtube.com/embed/UYiDTkKpRVo" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                        </div>
                        <div class="card-body">
                            <p class="card-text"><i class="bi bi-play-circle-fill me-1" style="color:var(--mep-gold);"></i>2) Agregar Marca</p>
                        </div>
                    </div>
                </div>
                <div class="col" id="ca">
                    <div class="card video-card">
                        <div class="ratio ratio-16x9">
                            <iframe src="https://www.youtube.com/embed/hKFPGRs6BAk" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                        </div>
                        <div class="card-body">
                            <p class="card-text"><i class="bi bi-play-circle-fill me-1" style="color:var(--mep-gold);"></i>3) Agregar Color</p>
                        </div>
                    </div>
                </div>
                <div class="col" id="sa">
                    <div class="card video-card">
                        <div class="ratio ratio-16x9">
                            <iframe src="https://www.youtube.com/embed/b69_OTP_t38" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                        </div>
                        <div class="card-body">
                            <p class="card-text"><i class="bi bi-play-circle-fill me-1" style="color:var(--mep-gold);"></i>4) Agregar Software</p>
                        </div>
                    </div>
                </div>
                <div class="col" id="tli">
                    <div class="card video-card">
                        <div class="ratio ratio-16x9">
                            <iframe src="https://www.youtube.com/embed/oWwIyErZvzc" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                        </div>
                        <div class="card-body">
                            <p class="card-text"><i class="bi bi-play-circle-fill me-1" style="color:var(--mep-gold);"></i>5) Agregar Tipo Licencia</p>
                        </div>
                    </div>
                </div>
                <div class="col" id="cs">
                    <div class="card video-card">
                        <div class="ratio ratio-16x9">
                            <iframe src="https://www.youtube.com/embed/fccfeO76jKw" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                        </div>
                        <div class="card-body">
                            <p class="card-text"><i class="bi bi-play-circle-fill me-1" style="color:var(--mep-gold);"></i>6) Agregar Caracter&iacutestica</p>
                        </div>
                    </div>
                </div>
                <div class="col" id="cel">
                    <div class="card video-card">
                        <div class="ratio ratio-16x9">
                            <iframe src="https://www.youtube.com/embed/PniikWR5bs0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                        </div>
                        <div class="card-body">
                            <p class="card-text"><i class="bi bi-play-circle-fill me-1" style="color:var(--mep-gold);"></i>7) Comprobaci&oacuten / Edici&oacuten de Licencia</p>
                        </div>
                    </div>
                </div>
                <div class="col" id="ofa">
                    <div class="card video-card">
                        <div class="ratio ratio-16x9">
                            <iframe src="https://www.youtube.com/embed/2i2XZdni24s" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                        </div>
                        <div class="card-body">
                            <p class="card-text"><i class="bi bi-play-circle-fill me-1" style="color:var(--mep-gold);"></i>8) Origen de Fondos de Adquisici&oacuten</p>
                        </div>
                    </div>
                </div>
                <div class="col" id="aaga">
                    <div class="card video-card">
                        <div class="ratio ratio-16x9">
                            <iframe src="https://www.youtube.com/embed/Fn9FgA0k9i0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                        </div>
                        <div class="card-body">
                            <p class="card-text"><i class="bi bi-play-circle-fill me-1" style="color:var(--mep-gold);"></i>9) Agregar Alias</p>
                        </div>
                    </div>
                </div>
                <div class="col" id="eana">
                    <div class="card video-card">
                        <div class="ratio ratio-16x9">
                            <iframe src="https://www.youtube.com/embed/9GFZm0IO9BE" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                        </div>
                        <div class="card-body">
                            <p class="card-text"><i class="bi bi-play-circle-fill me-1" style="color:var(--mep-gold);"></i>10) Editar Alias y Numeraci&oacuten de los Activos</p>
                        </div>
                    </div>
                </div>
                <div class="col" id="eps">
                    <div class="card video-card">
                        <div class="ratio ratio-16x9">
                            <iframe src="https://www.youtube.com/embed/VwN0LuFUWWU" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                        </div>
                        <div class="card-body">
                            <p class="card-text"><i class="bi bi-play-circle-fill me-1" style="color:var(--mep-gold);"></i>11) Editar Placa y Serie de Activo</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="section-divider"></div>

        <!-- ══════ SECCIÓN 4 — REPORTES ══════ -->
        <section id="s4" class="help-section">
            <div class="section-header">
                <h3><i class="bi bi-file-earmark-bar-graph-fill me-2" style="color:var(--mep-gold);"></i>Reportes</h3>
                <span class="badge-count">5 videos</span>
            </div>
            <div class="row g-4 row-cols-1 row-cols-md-2 row-cols-lg-3">
                <div class="col" id="rla">
                    <div class="card video-card">
                        <div class="ratio ratio-16x9">
                            <iframe src="https://www.youtube.com/embed/df6Ni8rxxNU" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                        </div>
                        <div class="card-body">
                            <p class="card-text"><i class="bi bi-play-circle-fill me-1" style="color:var(--mep-gold);"></i>1) Lista de activos</p>
                        </div>
                    </div>
                </div>
                <div class="col" id="rlad">
                    <div class="card video-card">
                        <div class="ratio ratio-16x9">
                            <iframe src="https://www.youtube.com/embed/jn1unx6A_qo" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                        </div>
                        <div class="card-body">
                            <p class="card-text"><i class="bi bi-play-circle-fill me-1" style="color:var(--mep-gold);"></i>2) Listado de Activos por Dependencia</p>
                        </div>
                    </div>
                </div>
                <div class="col" id="rladb">
                    <div class="card video-card">
                        <div class="ratio ratio-16x9">
                            <iframe src="https://www.youtube.com/embed/vltyv3ynXuk" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                        </div>
                        <div class="card-body">
                            <p class="card-text"><i class="bi bi-play-circle-fill me-1" style="color:var(--mep-gold);"></i>3) Listado de Activos Dados de Baja</p>
                        </div>
                    </div>
                </div>
                <div class="col" id="rladff">
                    <div class="card video-card">
                        <div class="ratio ratio-16x9">
                            <iframe src="https://www.youtube.com/embed/mtoAPbPJHvk" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                        </div>
                        <div class="card-body">
                            <p class="card-text"><i class="bi bi-play-circle-fill me-1" style="color:var(--mep-gold);"></i>4) Activos por Dependencia y Fuente de Financiamiento</p>
                        </div>
                    </div>
                </div>
                <div class="col" id="rlld">
                    <div class="card video-card">
                        <div class="ratio ratio-16x9">
                            <iframe src="https://www.youtube.com/embed/SoOor1DD_N8" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                        </div>
                        <div class="card-body">
                            <p class="card-text"><i class="bi bi-play-circle-fill me-1" style="color:var(--mep-gold);"></i>5) Listado de Licencias por Dependencia</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="section-divider"></div>

        <!-- ══════ SECCIÓN 5 — PRÉSTAMO ══════ -->
        <section id="s5" class="help-section">
            <div class="section-header">
                <h3><i class="bi bi-hand-index-thumb-fill me-2" style="color:var(--mep-gold);"></i>Pr&eacutestamo</h3>
                <span class="badge-count">4 videos</span>
            </div>
            <div class="row g-4 row-cols-1 row-cols-md-2 row-cols-lg-3">
                <div class="col" id="preone">
                    <div class="card video-card">
                        <div class="ratio ratio-16x9">
                            <iframe src="https://www.youtube.com/embed/cNU_a-KlBxY" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                        </div>
                        <div class="card-body">
                            <p class="card-text"><i class="bi bi-play-circle-fill me-1" style="color:var(--mep-gold);"></i>1) Solicitar Equipo</p>
                        </div>
                    </div>
                </div>
                <div class="col" id="pretwo">
                    <div class="card video-card">
                        <div class="ratio ratio-16x9">
                            <iframe src="https://www.youtube.com/embed/9sJ0QW9Zkpc" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                        </div>
                        <div class="card-body">
                            <p class="card-text"><i class="bi bi-play-circle-fill me-1" style="color:var(--mep-gold);"></i>2) Solicitudes Pendientes de Revisi&oacuten</p>
                        </div>
                    </div>
                </div>
                <div class="col" id="prethree">
                    <div class="card video-card">
                        <div class="ratio ratio-16x9">
                            <iframe src="https://www.youtube.com/embed/z6GiFYE6DeI" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                        </div>
                        <div class="card-body">
                            <p class="card-text"><i class="bi bi-play-circle-fill me-1" style="color:var(--mep-gold);"></i>3) Ver Art&iacuteculos Prestados y Recibirlos</p>
                        </div>
                    </div>
                </div>
                <div class="col" id="prefour">
                    <div class="card video-card">
                        <div class="ratio ratio-16x9">
                            <iframe src="https://www.youtube.com/embed/9sJ0QW9Zkpc" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                        </div>
                        <div class="card-body">
                            <p class="card-text"><i class="bi bi-play-circle-fill me-1" style="color:var(--mep-gold);"></i>4) Ver Art&iacuteculos Prestados y Recibirlos parte II</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="help-footer-spacer"></div>

    </div>

    <!-- ══════ SUBSISTEMAS ══════ -->
    <a class="btn-to-subsistemas" href="navegar.php?ruta=formulario_menu_principal.php" aria-label="Volver a Menú Principal">
        <i class="bi bi-grid-3x3-gap-fill"></i>
    </a>

    <!-- ══════ BACK TO TOP ══════ -->
    <button class="btn-to-top" id="btnToTop" onclick="window.scrollTo({top:0,behavior:'smooth'})" aria-label="Volver arriba">
        <i class="bi bi-chevron-up"></i>
    </button>

    <script>
    document.addEventListener("DOMContentLoaded", function () {
        var btn = document.getElementById("btnToTop");
        window.addEventListener("scroll", function () {
            btn.classList.toggle("show", window.scrollY > 400);
        });

        var navLinks = document.querySelectorAll(".help-nav .nav-link");
        navLinks.forEach(function (link) {
            link.addEventListener("click", function () {
                navLinks.forEach(function (l) { l.classList.remove("active"); });
                this.classList.add("active");
            });
        });

        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    var id = entry.target.getAttribute("id");
                    navLinks.forEach(function (l) {
                        l.classList.toggle("active", l.getAttribute("href") === "#" + id);
                    });
                }
            });
        }, { rootMargin: "-100px 0px -60% 0px" });

        document.querySelectorAll(".help-section").forEach(function (s) {
            observer.observe(s);
        });
    });
    </script>

    <?php include 'partials/footer.php'; ?>
</body>
</html>
