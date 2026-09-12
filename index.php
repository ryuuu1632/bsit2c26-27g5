<?php
session_start();
require 'config.php';
 
$isAdmin = !empty($_SESSION['admin']);
$loginFailed = isset($_GET['login']) && $_GET['login'] === 'fail';
 
$dataFile = __DIR__ . '/data/members.json';
$members = json_decode(file_get_contents($dataFile), true);
if (!is_array($members)) { $members = []; }
 
function h($str) { return htmlspecialchars((string)$str, ENT_QUOTES, 'UTF-8'); }
function initials($name) {
    $parts = preg_split('/\s+/', trim($name));
    $letters = '';
    foreach (array_slice($parts, 0, 2) as $p) { $letters .= mb_strtoupper(mb_substr($p, 0, 1)); }
    return $letters;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>BSIT 2C · Group 5 — Tagoloan Community College</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
<style>
  :root{
    /* ===== Token system ===== */
    --maroon-950:#3a0a14;
    --maroon-900:#4d0e1c;
    --maroon-800:#6e1428;
    --maroon-700:#8f1c35;
    --maroon-600:#b02545;
    --maroon-500:#cc3355;
    --gold-500:#d4a95f;
    --gold-400:#e6c589;
    --cream:#faf5ef;
    --paper:#fffdfb;
    --ink:#241016;
    --ink-soft:#5a3e46;
 
    --bg: var(--maroon-950);
    --bg-panel: var(--maroon-900);
    --bg-elevated: var(--maroon-800);
    --line: rgba(249,229,210,0.18);
    --text: #f9efe8;
    --text-soft: rgba(249,239,232,0.72);
    --accent: var(--gold-500);
    --card-bg: linear-gradient(165deg, var(--maroon-700), var(--maroon-950) 80%);
    --shadow: 0 30px 60px -25px rgba(0,0,0,0.55);
    --radius-lg: 22px;
    --radius-md: 14px;
    --radius-sm: 8px;
 
    --hero-gradient: linear-gradient(160deg, var(--maroon-700) 0%, var(--maroon-950) 90%);
 
    --font-display: 'Fraunces', serif;
    --font-body: 'Inter', sans-serif;
    --font-mono: 'JetBrains Mono', monospace;
  }
 
  [data-theme="midnight"]{
    --maroon-950:#0c0d10;
    --maroon-900:#141519;
    --maroon-800:#1e1f24;
    --maroon-700:#2a2b31;
    --maroon-600:#3a3b42;
    --maroon-500:#4d4e57;
    --gold-500:#c9a15a;
    --gold-400:#dcb877;
 
    --bg: #101114;
    --bg-panel: #17181c;
    --bg-elevated: #202126;
    --line: rgba(201,161,90,0.16);
    --text: #f0f0f2;
    --text-soft: rgba(240,240,242,0.62);
    --accent: var(--gold-500);
    --card-bg: linear-gradient(155deg, #202126, #101114 78%);
    --shadow: 0 30px 60px -25px rgba(0,0,0,0.75);
    --hero-gradient: linear-gradient(160deg, #202126 0%, #101114 90%);
  }
  [data-theme="midnight"] .id-badge{background:rgba(201,161,90,0.1); color:#dcb877; border-color:rgba(201,161,90,0.25);}
  [data-theme="midnight"] .btn-solid{color:#101114;}
  [data-theme="midnight"] .side-link.active{color:#101114;}
 
  *{box-sizing:border-box; margin:0; padding:0;}
  html{scroll-behavior:smooth;}
  body{
    background:
      radial-gradient(1200px 600px at 15% -10%, rgba(212,169,95,0.10), transparent 55%),
      radial-gradient(900px 500px at 100% 0%, rgba(176,37,69,0.25), transparent 60%),
      var(--bg);
    color:var(--text);
    font-family:var(--font-body);
    -webkit-font-smoothing:antialiased;
    transition:background .45s ease, color .45s ease;
    overflow-x:hidden;
  }
  img{max-width:100%; display:block;}
  a{color:inherit; text-decoration:none;}
  button{font-family:inherit; cursor:pointer;}
  ::selection{background:var(--accent); color:var(--maroon-950);}
 
  .eyebrow{
    font-family:var(--font-mono);
    font-size:11px;
    letter-spacing:.18em;
    text-transform:uppercase;
    color:var(--accent);
    display:flex; align-items:center; gap:8px;
  }
  .eyebrow::before{
    content:""; width:18px; height:1px; background:var(--accent);
  }
 
  /* ===== Layout shell ===== */
  .shell{
    max-width:1280px;
    margin:0 auto;
    padding:0 clamp(18px, 4vw, 48px);
  }
 
  /* ===== Header / Nav ===== */
  header.site-header{
    position:sticky; top:0; z-index:60;
    background:color-mix(in srgb, var(--bg) 88%, transparent);
    backdrop-filter:blur(14px);
    border-bottom:1px solid var(--line);
  }
  .nav-row{
    display:flex; align-items:center; justify-content:space-between;
    padding:16px clamp(18px, 4vw, 48px);
    max-width:1280px; margin:0 auto;
  }
  .brand{
    display:flex; align-items:center; gap:12px;
  }
  .brand-mark{
    width:42px; height:42px; border-radius:10px;
    background:linear-gradient(150deg, var(--gold-400), var(--maroon-600));
    display:flex; align-items:center; justify-content:center;
    font-family:var(--font-display); font-weight:700; font-size:15px;
    color:#2c0710;
    flex-shrink:0;
    box-shadow:0 6px 16px -6px rgba(0,0,0,.5);
  }
  .brand-text{line-height:1.15;}
  .brand-text strong{
    display:block; font-family:var(--font-display); font-size:16.5px; font-weight:600;
  }
  .brand-text span{
    display:block; font-family:var(--font-mono); font-size:10px; letter-spacing:.12em;
    color:var(--text-soft); text-transform:uppercase; margin-top:2px;
  }
 
  .theme-toggle{
    display:flex; align-items:center; gap:8px;
  }
  .swatch{
    width:20px; height:20px; border-radius:50%;
    border:2px solid transparent;
    padding:0; flex-shrink:0;
    transition:transform .2s ease, border-color .2s ease;
  }
  .swatch:hover{transform:scale(1.12);}
  .swatch.active{border-color:var(--text);}
  .swatch[data-theme-btn="dark"]{background:linear-gradient(135deg, #d4a95f, #8f1c35);}
  .swatch[data-theme-btn="midnight"]{background:linear-gradient(135deg, #3a3b42, #101114);}
 
  /* ===== Settings button + panel ===== */
  .settings-wrap{position:relative;}
  .settings-btn{
    width:40px; height:40px; border-radius:10px; border:1px solid var(--line);
    background:var(--bg-elevated); color:var(--text);
    display:flex; align-items:center; justify-content:center;
    font-size:16px; transition:border-color .2s ease, transform .3s ease;
  }
  .settings-btn:hover{border-color:var(--accent);}
  .settings-btn.spin svg{transform:rotate(75deg);}
  .settings-btn svg{transition:transform .35s ease; width:18px; height:18px;}
  .settings-panel{
    position:absolute; top:calc(100% + 10px); right:0; z-index:80;
    width:250px; background:var(--bg-panel); border:1px solid var(--line);
    border-radius:var(--radius-md); box-shadow:var(--shadow);
    padding:18px; display:none; flex-direction:column; gap:16px;
  }
  .settings-panel.open{display:flex;}
  .settings-group{display:flex; flex-direction:column; gap:10px;}
  .settings-group .eyebrow{margin-bottom:2px;}
  .settings-swatches{display:flex; align-items:center; gap:10px;}
  .settings-swatches .swatch{width:24px; height:24px;}
  .settings-swatches .swatch-label{
    font-family:var(--font-mono); font-size:10px; color:var(--text-soft);
    letter-spacing:.04em; text-transform:uppercase;
  }
  .settings-row{
    display:flex; align-items:center; justify-content:space-between; gap:10px;
  }
  .settings-row span{font-family:var(--font-mono); font-size:11.5px; color:var(--text); letter-spacing:.02em;}
  .toggle-switch{
    position:relative; width:38px; height:22px; border-radius:99px;
    background:var(--line); border:none; flex-shrink:0; transition:background .25s ease;
  }
  .toggle-switch::after{
    content:""; position:absolute; top:2px; left:2px; width:18px; height:18px;
    border-radius:50%; background:var(--text); transition:transform .25s ease;
  }
  .toggle-switch.on{background:var(--accent);}
  .toggle-switch.on::after{transform:translateX(16px); background:#2c0710;}
  .settings-divider{border-top:1px dashed var(--line);}
 
  nav.main-nav{
    display:flex; align-items:center; gap:6px;
  }
  nav.main-nav a{
    font-family:var(--font-mono); font-size:12px; letter-spacing:.08em; text-transform:uppercase;
    padding:9px 16px; border-radius:99px; color:var(--text-soft);
    transition:color .2s ease, background .2s ease;
  }
  nav.main-nav a:hover{color:var(--text); background:var(--bg-elevated);}
  nav.main-nav a.active{color:var(--accent); background:var(--bg-elevated); font-weight:600;}
 
  .btn{
    font-family:var(--font-mono); font-size:12px; letter-spacing:.06em; text-transform:uppercase;
    padding:10px 20px; border-radius:99px; border:1px solid var(--line);
    background:transparent; color:var(--text);
    display:inline-flex; align-items:center; gap:8px;
    transition:all .2s ease;
  }
  .btn:hover{border-color:var(--accent); color:var(--accent);}
  .btn-solid{
    background:linear-gradient(135deg, var(--gold-400), var(--maroon-500));
    border:none; color:#2c0710; font-weight:800;
    box-shadow:0 10px 28px -8px rgba(212,169,95,0.6);
    letter-spacing:.08em;
  }
  .btn-solid:hover{
    filter:brightness(1.12);
    transform:translateY(-2px);
    box-shadow:0 16px 34px -8px rgba(212,169,95,0.75);
    color:#2c0710;
  }
 
  .header-actions{display:flex; align-items:center; gap:14px;}
  .hamburger{
    display:none; width:40px; height:40px; border-radius:10px; border:1px solid var(--line);
    background:var(--bg-elevated); align-items:center; justify-content:center; flex-direction:column; gap:4px;
  }
  .hamburger span{width:18px; height:2px; background:var(--text); border-radius:2px;}
 
  /* ===== Hero shell (mirrors wireframe: sidebar + hero + slider) ===== */
  .hero-shell{
    max-width:1280px; margin:34px auto 0; padding:0 clamp(18px,4vw,48px);
  }
  .hero-frame{
    border:1px solid var(--line); border-radius:var(--radius-lg);
    overflow:hidden; background:var(--bg-panel);
    display:grid; grid-template-columns:230px 1fr;
    box-shadow:var(--shadow);
  }
 
  /* Sidebar */
  .sidebar{
    border-right:1px solid var(--line);
    padding:26px 20px;
    display:flex; flex-direction:column; gap:6px;
    background:var(--bg-panel);
  }
  .sidebar .eyebrow{margin-bottom:14px;}
  .side-link{
    font-family:var(--font-mono); font-size:12px; letter-spacing:.08em; text-transform:uppercase;
    padding:12px 14px; border-radius:var(--radius-sm);
    color:var(--text-soft); border:1px solid transparent;
    display:flex; align-items:center; justify-content:space-between;
    transition:all .2s ease;
  }
  .side-link:hover{color:var(--text); border-color:var(--line);}
  .side-link.active{
    background:linear-gradient(150deg, var(--gold-400), var(--maroon-600));
    color:#2c0710; font-weight:700; border-color:transparent;
  }
 
  /* Hero main */
  .hero-main{
    background:
      radial-gradient(800px 400px at 100% 0%, rgba(212,169,95,0.35), transparent 55%),
      radial-gradient(600px 500px at 0% 100%, rgba(176,37,69,0.4), transparent 60%),
      var(--hero-gradient);
    color:#f9efe8;
    padding:clamp(28px, 4vw, 46px);
    display:grid; grid-template-columns:0.85fr 1.4fr; gap:36px; align-items:center;
    position:relative;
    border-bottom:2px solid var(--gold-500);
  }
  .hero-main > *{ min-width:0; }
  .hero-copy .eyebrow{
    color:var(--gold-400);
    background:rgba(212,169,95,0.12);
    padding:6px 12px;
    border-radius:99px;
    border:1px solid rgba(212,169,95,0.3);
    display:inline-flex;
  }
  .hero-copy h1{
    font-family:var(--font-display); font-weight:700;
    font-size:clamp(30px, 4vw, 46px); line-height:1.06; margin:16px 0 10px;
    letter-spacing:-.01em;
    text-shadow:0 4px 24px rgba(0,0,0,0.35);
  }
  .hero-copy .lede{
    font-size:15px; color:rgba(249,239,232,0.85); max-width:46ch; line-height:1.6; margin-bottom:22px;
  }
  .hero-copy .lede strong{color:var(--gold-400); font-weight:700;}
  .hero-ctas{display:flex; gap:12px; flex-wrap:wrap;}
 
  .slider-col{
    display:flex; flex-direction:column; gap:16px; min-width:0;
  }
 
  /* Slider */
  .slider{
    position:relative; overflow:hidden;
   width:100%;
   max-width:100%;
   aspect-ratio:16/9;
   min-height:320px;
   perspective:1400px;
  }
  .slide{
   position:absolute; top:0; bottom:0; left:2%; right:2%;
   border-radius:var(--radius-md);
   overflow:hidden;
   transform-origin:center;
   transition:transform .7s cubic-bezier(.65,0,.35,1), filter .7s ease, opacity .7s ease;
   pointer-events:none;
   box-shadow:0 24px 50px -22px rgba(0,0,0,.65);
  }
 .slide.pos-current{
  transform:translateX(0) scale(1) rotateY(0deg);
  filter:none; opacity:1; z-index:3; pointer-events:auto;
  }
  .slide.pos-prev{
  transform:translateX(-78%) scale(0.7) rotateY(10deg);
  filter:blur(4px) brightness(.5); opacity:.6; z-index:2;
 
  }
  .slide.pos-next{
  transform:translateX(78%) scale(0.7) rotateY(-10deg);
  filter:blur(4px) brightness(.5); opacity:.6; z-index:2;
  }
  .slide.pos-hidden{
  transform:scale(0.42) translateY(6%);
  opacity:0; z-index:1;
  }
  .slide-blur{
    position:absolute; inset:-24px;
    background-size:cover;
    background-position:center;
    background-repeat:no-repeat;
    filter:blur(30px) brightness(0.55) saturate(1.15);
    transform:scale(1.15);
  }
  .slide-photo{
    position:absolute; inset:0;
    background-size:cover;
    background-position:center;
    background-repeat:no-repeat;
    z-index:1;
  }
 
  /* Caption card — sits directly below the photo slider, in its own flow (no overlap) */
  .caption-box{
    border-radius:var(--radius-md);
    border:1px solid rgba(212,169,95,0.25);
    background:var(--bg-elevated);
    box-shadow:var(--shadow);
    padding:16px 18px; min-height:96px;
    display:flex; flex-direction:column; justify-content:center; gap:4px;
    position:relative; overflow:hidden;
  }
  .caption-box::before{
    content:"";
    position:absolute; left:0; top:0; bottom:0; width:4px;
    background:linear-gradient(180deg, var(--gold-400), var(--maroon-600));
  }
  .caption-box .eyebrow{color:var(--gold-400);}
  .caption-box .eyebrow, .caption-box h3, .caption-box p{
    transition:opacity .35s ease, transform .35s ease;
  }
  .caption-box.fade-out .eyebrow,
  .caption-box.fade-out h3,
  .caption-box.fade-out p{
    opacity:0; transform:translateY(6px);
  }
  .caption-box h3{font-family:var(--font-display); font-size:17px; font-weight:600; margin:4px 0 2px;}
  .caption-box p{font-size:12.5px; color:var(--text-soft); line-height:1.55; max-width:46ch;}
  .caption-dots{display:flex; gap:6px; margin-top:10px;}
  .caption-dots button{
    width:6px; height:6px; border-radius:50%; border:none; padding:0;
    background:var(--line); transition:all .25s ease;
  }
  .caption-dots button.active{background:var(--accent); width:18px; border-radius:5px;}
  .slide-nav{
    position:absolute; top:50%; transform:translateY(-50%);
    width:36px; height:36px; border-radius:50%;
    background:rgba(0,0,0,0.32); border:1px solid rgba(249,229,210,0.25);
    color:#f7ece4; display:flex; align-items:center; justify-content:center;
    font-size:16px; z-index:7; transition:background .2s ease;
  }
  .slide-nav:hover{background:rgba(0,0,0,0.55);}
  .slide-prev{left:14px;} .slide-next{right:14px;}
  .slide-dots{
    display:none;
  }
  .slide-dots button{
    width:7px; height:7px; border-radius:50%; border:none; padding:0;
    background:rgba(249,229,210,0.35); transition:all .25s ease;
  }
  .slide-dots button.active{background:var(--gold-400); width:20px; border-radius:5px;}
 
  /* ===== Section shared ===== */
  section{padding:78px 0;}
  .section-head{
    display:flex; align-items:flex-end; justify-content:space-between; gap:20px; flex-wrap:wrap;
    margin-bottom:38px;
  }
  .section-head h2{
    font-family:var(--font-display); font-weight:600;
    font-size:clamp(26px,3vw,36px); margin-top:10px; letter-spacing:-.01em;
    position:relative;
    padding-bottom:16px;
  }
  .section-head h2::after{
    content:"";
    position:absolute; left:0; bottom:0;
    width:80px; height:4px; border-radius:99px;
    background:linear-gradient(90deg, var(--gold-500), var(--gold-400));
    box-shadow:0 2px 12px rgba(212,169,95,0.5);
  }
  .section-head p{color:var(--text-soft); font-size:14px; max-width:44ch; margin-top:8px;}
 
  /* ===== Member ID cards (signature element) ===== */
  .members-grid{
    display:grid; grid-template-columns:repeat(5, 1fr); gap:18px;
  }
  .id-card{
    position:relative;
    background:var(--card-bg);
    border:1px solid rgba(212,169,95,0.25);
    border-radius:var(--radius-md);
    padding:0;
    overflow:hidden;
    box-shadow:0 20px 40px -20px rgba(0,0,0,0.6);
    transition:transform .3s ease, border-color .3s ease, box-shadow .3s ease;
  }
  .id-card:hover{
    transform:translateY(-8px) scale(1.015);
    border-color:var(--gold-500);
    box-shadow:0 30px 50px -20px rgba(0,0,0,0.7), 0 0 30px -5px rgba(212,169,95,0.4);
  }
  .id-card::before{
    /* lanyard hole */
    content:""; position:absolute; top:14px; left:50%; transform:translateX(-50%);
    width:26px; height:6px; border-radius:99px; background:var(--bg-elevated);
    border:1px solid var(--line); z-index:3;
  }
  .id-stripe{
    height:10px; width:100%;
    background:repeating-linear-gradient(115deg, var(--gold-400) 0 10px, var(--maroon-600) 10px 20px);
    opacity:1;
  }
  .id-badge{
    position:absolute; top:26px; right:12px; z-index:3;
    font-family:var(--font-mono); font-size:9px; letter-spacing:.06em;
    background:rgba(0,0,0,0.28); color:var(--gold-400);
    border:1px solid rgba(249,229,210,0.25);
    padding:3px 8px; border-radius:99px;
  }
 
  .photo-wrap{
    margin:30px 18px 0; aspect-ratio:1/1; border-radius:12px; overflow:hidden;
    border:1px solid var(--line); position:relative;
    background:var(--bg-elevated);
  }
  .photo-wrap img{width:100%; height:100%; object-fit:cover;}
  .avatar-fallback{
    width:100%; height:100%; display:flex; align-items:center; justify-content:center;
    font-family:var(--font-display); font-size:34px; font-weight:600; color:#fff;
    background:linear-gradient(150deg, var(--gold-400), var(--maroon-600));
  }
 
  .id-info{padding:16px 18px 20px;}
  .id-info .m-name{font-family:var(--font-display); font-size:18px; font-weight:600;}
  .id-info .m-role{
    font-family:var(--font-mono); font-size:10.5px; letter-spacing:.06em; text-transform:uppercase;
    color:var(--accent); margin-top:4px;
  }
  .m-skills{
    display:flex; flex-wrap:wrap; gap:5px; margin-top:12px;
  }
  .m-skills span{
    font-family:var(--font-mono); font-size:9.5px; letter-spacing:.03em;
    padding:4px 8px; border-radius:6px; border:1px solid var(--line); color:var(--text-soft);
  }
  .m-social{display:flex; gap:8px; margin-top:14px;}
  .m-social a{
    width:26px; height:26px; border-radius:50%; border:1px solid var(--line);
    display:flex; align-items:center; justify-content:center; font-size:11px; color:var(--text-soft);
    transition:all .2s ease;
  }
  .m-social a:hover{color:var(--accent); border-color:var(--accent);}

  .view-info-btn{
    width:100%; margin-top:16px; padding:10px;
    font-family:var(--font-mono); font-size:10.5px; letter-spacing:.08em; text-transform:uppercase;
    border-radius:8px; border:1px solid rgba(212,169,95,0.35);
    background:rgba(212,169,95,0.08); color:var(--accent);
    transition:all .2s ease;
  }
  .view-info-btn:hover{
    background:var(--accent); color:#2c0710; border-color:var(--accent);
  }

  /* ===== Member info modal ===== */
  .info-modal-overlay{
    position:fixed; inset:0; background:rgba(15,3,7,0.72); backdrop-filter:blur(6px);
    display:none; align-items:center; justify-content:center; z-index:200; padding:20px;
  }
  .info-modal-overlay.open{display:flex;}
  .info-modal{
    width:100%; max-width:440px; background:var(--bg-panel); border:1px solid rgba(212,169,95,0.25);
    border-radius:var(--radius-lg); padding:0; box-shadow:var(--shadow); position:relative;
    overflow:hidden; max-height:86vh; display:flex; flex-direction:column;
  }
  .info-modal-close{
    position:absolute; top:16px; right:16px; z-index:5;
    width:32px; height:32px; border-radius:50%;
    border:1px solid rgba(249,229,210,0.3); background:rgba(0,0,0,0.35); color:#f7ece4; font-size:14px;
  }
  .info-modal-head{
    background:
      radial-gradient(500px 260px at 100% -10%, rgba(212,169,95,0.3), transparent 60%),
      var(--hero-gradient);
    padding:28px 24px; display:flex; align-items:center; gap:16px;
    border-bottom:2px solid var(--gold-500);
  }
  .info-modal-photo{
    width:76px; height:76px; border-radius:14px; overflow:hidden; flex-shrink:0;
    border:1px solid rgba(249,229,210,0.3);
    background:var(--bg-elevated);
  }
  .info-modal-photo img{width:100%; height:100%; object-fit:cover;}
  .info-modal-photo .avatar-fallback{font-size:26px;}
  .info-modal-id .m-name{font-family:var(--font-display); font-size:21px; font-weight:600; color:#f9efe8;}
  .info-modal-id .m-role{
    font-family:var(--font-mono); font-size:10.5px; letter-spacing:.06em; text-transform:uppercase;
    color:var(--gold-400); margin-top:5px;
  }
  .info-modal-body{padding:22px 24px 26px; overflow-y:auto;}
  .info-modal-section{margin-bottom:18px;}
  .info-modal-section:last-child{margin-bottom:0;}
  .info-modal-section .eyebrow{margin-bottom:8px;}
  .info-modal-section p{font-size:13.5px; color:var(--text-soft); line-height:1.7;}
  .info-modal-skills{display:flex; flex-wrap:wrap; gap:6px;}
  .info-modal-skills span{
    font-family:var(--font-mono); font-size:10px; letter-spacing:.03em;
    padding:5px 10px; border-radius:6px; border:1px solid var(--line); color:var(--text-soft);
  }
 
  .id-edit-btn{
    position:absolute; top:26px; left:12px; z-index:4;
    width:26px; height:26px; border-radius:50%;
    background:var(--accent); color:#2c0710; border:none;
    display:none; align-items:center; justify-content:center; font-size:12px;
  }
  body.admin-on .id-edit-btn{display:flex;}
 
  /* Edit form overlay */
  .edit-panel{
    display:none; padding:16px 18px 20px; border-top:1px dashed var(--line);
    background:var(--bg-elevated);
  }
  .edit-panel.open{display:block;}
  .edit-panel label{
    font-family:var(--font-mono); font-size:9.5px; letter-spacing:.06em; text-transform:uppercase;
    color:var(--text-soft); display:block; margin:10px 0 4px;
  }
  .edit-panel input[type="text"], .edit-panel input[type="url"]{
    width:100%; padding:8px 10px; border-radius:8px; border:1px solid var(--line);
    background:var(--bg-panel); color:var(--text); font-family:var(--font-body); font-size:12.5px;
  }
  .edit-panel .file-row{display:flex; gap:8px; align-items:center; margin-top:4px;}
  .edit-panel .file-row label.file-btn{
    margin:0; font-family:var(--font-mono); font-size:10px; padding:8px 10px; border:1px solid var(--line);
    border-radius:8px; cursor:pointer; text-transform:uppercase;
  }
  .edit-panel input[type="file"]{display:none;}
  .edit-actions{display:flex; gap:8px; margin-top:14px;}
  .edit-actions button{flex:1; padding:8px; border-radius:8px; font-family:var(--font-mono); font-size:10.5px; text-transform:uppercase; border:1px solid var(--line); background:transparent; color:var(--text);}
  .edit-actions .save-btn{background:var(--accent); color:#2c0710; border:none; font-weight:700;}
 
  /* ===== Group info / contact ===== */
  .split{display:grid; grid-template-columns:1fr 1fr; gap:40px; align-items:center;}
  .info-card{
    background:var(--bg-panel); border:1px solid rgba(212,169,95,0.2); border-radius:var(--radius-md);
    padding:26px; box-shadow:var(--shadow);
    position:relative; overflow:hidden;
  }
  .info-card::after{
    content:"";
    position:absolute; top:-40%; right:-20%; width:180px; height:180px; border-radius:50%;
    background:radial-gradient(circle, rgba(212,169,95,0.15), transparent 70%);
    pointer-events:none;
  }
  .info-card h3{font-family:var(--font-display); font-size:19px; margin-bottom:10px;}
  .info-card p{color:var(--text-soft); font-size:14px; line-height:1.7;}
  .stat-row{display:flex; gap:24px; margin-top:20px; flex-wrap:wrap;}
  .stat-row div strong{font-family:var(--font-display); font-size:26px; display:block; color:var(--accent);}
  .stat-row div span{font-family:var(--font-mono); font-size:10px; letter-spacing:.06em; text-transform:uppercase; color:var(--text-soft);}
 
  .contact-grid{display:grid; grid-template-columns:repeat(3,1fr); gap:16px;}
  .contact-item{
    background:var(--bg-panel); border:1px solid var(--line); border-radius:var(--radius-md);
    padding:22px; text-align:left;
    transition:border-color .25s ease, transform .25s ease;
  }
  .contact-item:hover{border-color:var(--gold-500); transform:translateY(-4px);}
  .contact-item .eyebrow{margin-bottom:10px;}
  .contact-item strong{font-family:var(--font-display); font-size:16px; display:block; margin-bottom:6px;}
  .contact-item span{color:var(--text-soft); font-size:13px;}
 
  footer{
    border-top:1px solid var(--line); padding:26px clamp(18px,4vw,48px);
    display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;
    font-family:var(--font-mono); font-size:11.5px; color:var(--text-soft);
  }
 
  /* ===== Modal ===== */
  .modal-overlay{
    position:fixed; inset:0; background:rgba(15,3,7,0.72); backdrop-filter:blur(6px);
    display:none; align-items:center; justify-content:center; z-index:200; padding:20px;
  }
  .modal-overlay.open{display:flex;}
  .modal{
    width:100%; max-width:380px; background:var(--bg-panel); border:1px solid var(--line);
    border-radius:var(--radius-lg); padding:30px; box-shadow:var(--shadow); position:relative;
  }
  .modal-close{
    position:absolute; top:16px; right:16px; width:30px; height:30px; border-radius:50%;
    border:1px solid var(--line); background:transparent; color:var(--text); font-size:14px;
  }
  .modal .eyebrow{margin-bottom:10px;}
  .modal h3{font-family:var(--font-display); font-size:22px; margin-bottom:6px;}
  .modal .sub{color:var(--text-soft); font-size:12.5px; margin-bottom:20px;}
  .field{margin-bottom:14px;}
  .field label{
    font-family:var(--font-mono); font-size:10px; letter-spacing:.06em; text-transform:uppercase;
    color:var(--text-soft); display:block; margin-bottom:6px;
  }
  .field input{
    width:100%; padding:11px 12px; border-radius:9px; border:1px solid var(--line);
    background:var(--bg-elevated); color:var(--text); font-family:var(--font-body); font-size:13.5px;
  }
  .field input:focus, .edit-panel input:focus{outline:2px solid var(--accent); outline-offset:1px;}
  .modal .hint{
    font-family:var(--font-mono); font-size:10.5px; color:var(--text-soft); margin-top:14px;
    border-top:1px dashed var(--line); padding-top:12px; line-height:1.6;
  }
  .modal .error-msg{
    display:none; font-family:var(--font-mono); font-size:11px; color:#ff9d9d; margin-top:10px;
  }
  .admin-pill{
    display:none; align-items:center; gap:8px; font-family:var(--font-mono); font-size:11px;
    padding:8px 14px; border-radius:99px; border:1px solid var(--accent); color:var(--accent);
  }
  body.admin-on .admin-pill{display:inline-flex;}
  .dot-live{width:6px; height:6px; border-radius:50%; background:var(--accent); box-shadow:0 0 0 3px color-mix(in srgb, var(--accent) 30%, transparent);}
 
  /* ===== Mobile nav drawer ===== */
  .mobile-drawer{
    position:fixed; inset:0 0 0 auto; width:min(320px,84vw); background:var(--bg-panel);
    border-left:1px solid var(--line); transform:translateX(100%); transition:transform .35s cubic-bezier(.65,0,.35,1);
    z-index:210; padding:26px 22px; display:flex; flex-direction:column; gap:8px;
  }
  .mobile-drawer.open{transform:translateX(0);}
  .drawer-overlay{
    position:fixed; inset:0; background:rgba(0,0,0,.5); z-index:205; display:none;
  }
  .drawer-overlay.open{display:block;}
  .mobile-drawer a, .mobile-drawer button.btn{
    padding:14px 10px; border-bottom:1px solid var(--line); font-family:var(--font-mono);
    font-size:13px; letter-spacing:.06em; text-transform:uppercase; text-align:left;
  }
 
  /* ===== Responsive ===== */
  @media (max-width: 980px){
    nav.main-nav{display:none;}
    .hamburger{display:flex;}
    .hero-frame{grid-template-columns:1fr;}
    .sidebar{
      flex-direction:row; overflow-x:auto; border-right:none; border-bottom:1px solid var(--line);
      padding:16px;
    }
    .hero-main{grid-template-columns:1fr;}
    .slider{aspect-ratio:16/9;}
    .members-grid{grid-template-columns:repeat(3,1fr);}
    .split{grid-template-columns:1fr;}
    .contact-grid{grid-template-columns:1fr 1fr;}
  }
  @media (max-width: 640px){
    .members-grid{grid-template-columns:repeat(2,1fr);}
    .contact-grid{grid-template-columns:1fr;}
    .hero-copy .lede{max-width:none;}
    .brand-text span{display:none;}
  }
  @media (max-width: 420px){
    .members-grid{grid-template-columns:1fr;}
  }
 
  @media (prefers-reduced-motion: reduce){
    *{transition:none !important; scroll-behavior:auto !important;}
  }
  body.force-reduce-motion *{transition:none !important; scroll-behavior:auto !important; animation:none !important;}
</style>
</head>
<body<?php echo $isAdmin ? ' class="admin-on"' : ''; ?> data-theme="dark">
 
  <!-- ===================== HEADER ===================== -->
  <header class="site-header">
    <div class="nav-row">
      <div class="brand">
        <div class="brand-mark">TCC</div>
        <div class="brand-text">
          <strong>Group 5 · BSIT 2C</strong>
          <span>Tagoloan Community College</span>
        </div>
      </div>
 
      <nav class="main-nav" aria-label="Primary">
        <a href="#top" class="active">Home</a>
        <a href="#members">Members</a>
        <a href="#group-info">Group Info</a>
        <a href="#contact">Contact</a>
      </nav>
 <div class="header-actions">
        <div class="settings-wrap">
          <button class="settings-btn" id="settingsBtn" type="button" aria-label="Settings" title="Settings">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
          </button>
          <div class="settings-panel" id="settingsPanel">
            <div class="settings-group">
              <span class="eyebrow">Theme</span>
              <div class="settings-swatches" id="themeSwatches">
                <button class="swatch" data-theme-btn="dark" aria-label="Default theme" title="Default"></button>
                <button class="swatch" data-theme-btn="midnight" aria-label="Dark mode theme" title="Dark Mode"></button>
              </div>
            </div>
            <div class="settings-divider"></div>
            <div class="settings-group">
              <span class="eyebrow">Slideshow</span>
              <div class="settings-row">
                <span>Autoplay</span>
                <button class="toggle-switch on" id="autoplayToggle" type="button" aria-label="Toggle autoplay"></button>
              </div>
            </div>
            <div class="settings-divider"></div>
            <div class="settings-group">
              <span class="eyebrow">Accessibility</span>
              <div class="settings-row">
                <span>Reduce motion</span>
                <button class="toggle-switch" id="motionToggle" type="button" aria-label="Toggle reduce motion"></button>
              </div>
            </div>
          </div>
        </div>
        <span class="admin-pill" id="adminPill"><span class="dot-live"></span>Admin mode</span>
      <?php if ($isAdmin): ?>
      <a href="admin/index.php" class="btn">Dashboard</a>
      <a href="logout.php" class="btn btn-solid">Logout</a>
      <?php else: ?>
      <a href="login.php">Login</a>
      <?php endif; ?>
        <button class="hamburger" id="hamburgerBtn" aria-label="Open menu"><span></span><span></span><span></span></button>
      </div>
    </div>
  </header>
      
 
  <div class="drawer-overlay" id="drawerOverlay"></div>
 <div class="mobile-drawer" id="mobileDrawer">
    <span class="eyebrow">Navigate</span>
    <a href="#top">Home</a>
    <a href="#members">Members</a>
    <a href="#group-info">Group Info</a>
    <a href="#contact">Contact</a>
    <?php if ($isAdmin): ?>
      <a href="admin/dashboard.php">Admin Dashboard</a>
    <?php endif; ?>
</div>
 
  <!-- ===================== HERO ===================== -->
  <div class="hero-shell" id="top">
    <div class="hero-frame">
 
      <aside class="sidebar">
        <span class="eyebrow">Menu</span>
        <a class="side-link active" href="#top">Profile</a>
        <a class="side-link" href="#group-info">Group Info</a>
        <a class="side-link" href="#contact">Contact</a>
      </aside>
 
      <div class="hero-main">
        <div class="hero-copy">
          <span class="eyebrow">BSIT 2C — Group 5</span>
          <h1>Five students,<br>one build.</h1>
          <p class="lede">A portfolio site introducing <strong>Group 5</strong> of BSIT&nbsp;2C — Tagoloan Community College. Built by the team, for the team: our roles, our skills, our work.</p>
          <div class="hero-ctas">
            <a href="#members" class="btn btn-solid">View Team</a>
            <a href="#group-info" class="btn">Group Info</a>
          </div>
        </div>
 
        <!-- Right column: photo slideshow + caption card stacked together -->
        <div class="slider-col">
          <div class="slider" id="slider">
            <div class="slide pos-current" aria-label="Innovative Solutions">
              <div class="slide-blur" style="background-image:url('images/slides1.jpg');"></div>
             <div class="slide-photo" style="background-image:url('images/slides1.jpg');"></div>
            </div>
            <div class="slide pos-next" aria-label="Team Collaboration">
              <div class="slide-blur" style="background-image:url('images/slides2.jpg');"></div>
              <div class="slide-photo" style="background-image:url('images/slides2.jpg');"></div>
            </div>
            <div class="slide pos-prev" aria-label="TCC IT Excellence">
              <div class="slide-blur" style="background-image:url('images/slides3.jpg');"></div>
              <div class="slide-photo" style="background-image:url('images/slides3.jpg');"></div>
            </div>
            <button class="slide-nav slide-prev" aria-label="Previous slide">&#8249;</button>
            <button class="slide-nav slide-next" aria-label="Next slide">&#8250;</button>
            <div class="slide-dots" id="sliderDots"></div>
          </div>
 
          <!-- Caption card: text-only, sits neatly below the photo, synced to whichever slide is active -->
          <div class="caption-box" id="captionBox">
            <span class="eyebrow">Slide 01</span>
            <h3>Tagoloan Community College (TCC)</h3>
            <p>Tagoloan Community College (TCC) is a premier local tertiary institution in Misamis Oriental, committed to delivering accessible, high-quality higher education to local youth and community learners.
 
</p>
            <div class="caption-dots" id="captionDots"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
 
 
 
  <!-- ===================== MEMBERS ===================== -->
  <section id="members">
    <div class="shell">
      <div class="section-head">
        <div>
          <span class="eyebrow">The team</span>
          <h2>Meet Group 5</h2>
          <p>BSIT 2C, Tagoloan Community College.</p>
        </div>
      </div>
      <div class="members-grid" id="membersGrid">
        <?php foreach ($members as $idx => $m): ?>
        <article class="id-card">
          <div class="id-stripe"></div>
          <span class="id-badge">ID · 0<?php echo $idx + 1; ?></span>
          <div class="photo-wrap">
            <?php if (!empty($m['photo'])): ?>
              <img src="<?php echo h($m['photo']); ?>" alt="<?php echo h($m['name']); ?> photo">
            <?php else: ?>
              <div class="avatar-fallback"><?php echo h(initials($m['name'])); ?></div>
            <?php endif; ?>
          </div>
          <div class="id-info">
            <div class="m-name"><?php echo h($m['name']); ?></div>
            <div class="m-role"><?php echo h($m['role']); ?></div>
            <div class="m-skills">
              <?php foreach ((array)$m['skills'] as $s): ?><span><?php echo h($s); ?></span><?php endforeach; ?>
            </div>
            <div class="m-social">
              <a href="#" aria-label="X profile" onclick="return false;">&#120143;</a>
              <a href="#" aria-label="Facebook profile" onclick="return false;">f</a>
              <a href="#" aria-label="Instagram profile" onclick="return false;">&#9679;</a>
            </div>
            <button
              type="button"
              class="view-info-btn"
              data-member="<?php echo h(json_encode([
                'name'   => $m['name'] ?? '',
                'role'   => $m['role'] ?? '',
                'photo'  => $m['photo'] ?? '',
                'skills' => array_values((array)($m['skills'] ?? [])),
                'bio'    => $m['bio'] ?? '',
                'email'  => $m['email'] ?? '',
                'idNum'  => 'ID · 0' . ($idx + 1),
                'initials' => initials($m['name'] ?? ''),
              ])); ?>"
            >View Info</button>
          </div>
        </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
 
  <!-- ===================== MEMBER INFO MODAL ===================== -->
  <div class="info-modal-overlay" id="infoModalOverlay">
    <div class="info-modal">
      <button class="info-modal-close" id="infoModalClose" aria-label="Close">&#10005;</button>
      <div class="info-modal-head">
        <div class="info-modal-photo" id="infoModalPhotoWrap"></div>
        <div class="info-modal-id">
          <div class="m-name" id="infoModalName"></div>
          <div class="m-role" id="infoModalRole"></div>
        </div>
      </div>
      <div class="info-modal-body">
        <div class="info-modal-section" id="infoModalBioSection">
          <span class="eyebrow">About</span>
          <p id="infoModalBio"></p>
        </div>
        <div class="info-modal-section" id="infoModalSkillsSection">
          <span class="eyebrow">Skills</span>
          <div class="info-modal-skills" id="infoModalSkills"></div>
        </div>
        <div class="info-modal-section" id="infoModalEmailSection">
          <span class="eyebrow">Contact</span>
          <p id="infoModalEmail"></p>
        </div>
      </div>
    </div>
  </div>

  <!-- ===================== GROUP INFO ===================== -->
  <section id="group-info">
    <div class="shell">
      <div class="split">
        <div>
          <span class="eyebrow">Who we are</span>
          <h2 style="font-family:var(--font-display); font-weight:600; font-size:clamp(24px,3vw,32px); margin:12px 0 14px;">Group 5, BSIT 2C</h2>
          <p style="color:var(--text-soft); line-height:1.75; font-size:14.5px; max-width:52ch;">
            Group 5 is a five-member team from the BSIT 2C block at Tagoloan Community College. This site itself is our group project — a live demonstration of front-end structure, responsive layout, and role-based teamwork, built and maintained by the members you see below.
          </p>
        </div>
        <div class="info-card">
          <h3>Class Snapshot</h3>
          <p>Section BSIT 2C · Academic Year 2026–2027 · Tagoloan Community College, Tagoloan, Misamis Oriental.</p>
          <div class="stat-row">
            <div><strong>5</strong><span>Members</span></div>
            <div><strong>2C</strong><span>Section</span></div>
            <div><strong>05</strong><span>Group No.</span></div>
            <div><strong>2026</strong><span>School Year</span></div>
          </div>
        </div>
      </div>
    </div>
  </section>
 
  <!-- ===================== CONTACT ===================== -->
  <section id="contact">
    <div class="shell">
      <div class="section-head">
        <div>
          <span class="eyebrow">Get in touch</span>
          <h2>Contact</h2>
        </div>
      </div>
      <div class="contact-grid">
        <div class="contact-item">
          <span class="eyebrow">Institution</span>
          <strong>Tagoloan Community College</strong>
          <span>Baluarte, Tagoloan, Misamis Oriental</span>
        </div>
        <div class="contact-item">
          <span class="eyebrow">Program</span>
          <strong>BSIT 2C</strong>
          <span>Bachelor of Science in Information Technology</span>
        </div>
        <div class="contact-item">
          <span class="eyebrow">Group</span>
          <strong>Group 5</strong>
          <span>See member cards above for individual contacts</span>
        </div>
      </div>
    </div>
  </section>
 
  <footer>
    <span>Group 5 | Tagoloan Community College | 2026</span>
    <span>BSIT 2C — Website Structure &amp; Components</span>
  </footer>
 
  <!-- ===================== LOGIN MODAL ===================== -->
  
 
<script>
(function(){
  "use strict";
 
  /* ---------- Theme ---------- */
  const root = document.body;
  const swatchBtns = document.querySelectorAll('[data-theme-btn]');
  function applyTheme(t){
    root.setAttribute('data-theme', t);
    localStorage.setItem('tcc_theme', t);
    swatchBtns.forEach(b => b.classList.toggle('active', b.dataset.themeBtn === t));
  }
  applyTheme(localStorage.getItem('tcc_theme') || 'dark');
  swatchBtns.forEach(b => b.addEventListener('click', () => applyTheme(b.dataset.themeBtn)));
 
  /* ---------- Settings panel ---------- */
  const settingsBtn = document.getElementById('settingsBtn');
  const settingsPanel = document.getElementById('settingsPanel');
  function toggleSettings(open){
    const willOpen = open !== undefined ? open : !settingsPanel.classList.contains('open');
    settingsPanel.classList.toggle('open', willOpen);
    settingsBtn.classList.toggle('spin', willOpen);
  }
  settingsBtn.addEventListener('click', (e) => { e.stopPropagation(); toggleSettings(); });
  document.addEventListener('click', (e) => {
    if(settingsPanel.classList.contains('open') && !settingsPanel.contains(e.target) && e.target !== settingsBtn){
      toggleSettings(false);
    }
  });
  document.addEventListener('keydown', (e) => { if(e.key === 'Escape') toggleSettings(false); });
 
  /* ---------- Autoplay toggle ---------- */
  const autoplayToggle = document.getElementById('autoplayToggle');
  let autoplayOn = localStorage.getItem('tcc_autoplay') !== 'off';
  function applyAutoplayState(){
    autoplayToggle.classList.toggle('on', autoplayOn);
    if(autoplayOn) startAuto(); else clearInterval(sliderTimer);
  }
  autoplayToggle.addEventListener('click', () => {
    autoplayOn = !autoplayOn;
    localStorage.setItem('tcc_autoplay', autoplayOn ? 'on' : 'off');
    applyAutoplayState();
  });
 
  /* ---------- Reduce motion toggle ---------- */
  const motionToggle = document.getElementById('motionToggle');
  let reduceMotion = localStorage.getItem('tcc_reduce_motion') === 'on';
  function applyMotionState(){
    motionToggle.classList.toggle('on', reduceMotion);
    document.body.classList.toggle('force-reduce-motion', reduceMotion);
  }
  motionToggle.addEventListener('click', () => {
    reduceMotion = !reduceMotion;
    localStorage.setItem('tcc_reduce_motion', reduceMotion ? 'on' : 'off');
    applyMotionState();
  });
  applyMotionState();
 
  /* ---------- Mobile drawer ---------- */
  const drawer = document.getElementById('mobileDrawer');
  const drawerOverlay = document.getElementById('drawerOverlay');
  function toggleDrawer(open){
    drawer.classList.toggle('open', open);
    drawerOverlay.classList.toggle('open', open);
  }
  document.getElementById('hamburgerBtn').addEventListener('click', () => toggleDrawer(true));
  drawerOverlay.addEventListener('click', () => toggleDrawer(false));
  drawer.querySelectorAll('a').forEach(a => a.addEventListener('click', () => toggleDrawer(false)));
 
  /* ---------- Slider ---------- */
  const slidesData = [
    { eyebrow: 'Slide 01', title: 'Tagoloan Community College (TCC)', text: 'Tagoloan Community College (TCC) is a premier local tertiary institution in Misamis Oriental, committed to delivering accessible, high-quality higher education to local youth and community learners.' },
    { eyebrow: 'Slide 02', title: 'Bachelor of Science in Information Technology (BSIT)', text: 'The Bachelor of Science in Information Technology (BSIT) program at TCC equips students with industry-relevant competencies in software development, database administration, networking, and system security—preparing graduates to meet the evolving demands of the global tech landscape.' },
    { eyebrow: 'Slide 03', title: 'TCC IT Excellence', text: 'Representing BSIT 2C with clean code, sharp design, and steady delivery.' }
  ];
 
  const slides = Array.from(document.querySelectorAll('.slide'));
  const dotsWrap = document.getElementById('sliderDots');
  const captionBox = document.getElementById('captionBox');
  const captionDotsWrap = document.getElementById('captionDots');
  let current = 0, sliderTimer;
 
  function buildDots(wrap){
    slides.forEach((_, i) => {
      const d = document.createElement('button');
      if(i === 0) d.classList.add('active');
      d.addEventListener('click', () => { goTo(i); if(autoplayOn) startAuto(); });
      wrap.appendChild(d);
    });
    return Array.from(wrap.children);
  }
  const dots = buildDots(dotsWrap);
  const captionDots = buildDots(captionDotsWrap);
 
  function renderCaption(i){
    const d = slidesData[i];
    if(!d || !captionBox) return;
    captionBox.classList.add('fade-out');
    setTimeout(() => {
      captionBox.querySelector('.eyebrow').textContent = d.eyebrow;
      captionBox.querySelector('h3').textContent = d.title;
      captionBox.querySelector('p').textContent = d.text;
      captionBox.classList.remove('fade-out');
    }, 220);
  }
 
  function goTo(i){
    const total = slides.length;
    current = (i + total) % total;
    const prevIdx = (current - 1 + total) % total;
    const nextIdx = (current + 1) % total;
 
    slides.forEach((s, idx) => {
      s.classList.remove('pos-current', 'pos-prev', 'pos-next', 'pos-hidden');
      if(idx === current) s.classList.add('pos-current');
      else if(idx === prevIdx) s.classList.add('pos-prev');
      else if(idx === nextIdx) s.classList.add('pos-next');
      else s.classList.add('pos-hidden');
    });
 
    dots.forEach(d => d.classList.remove('active'));
    captionDots.forEach(d => d.classList.remove('active'));
    dots[current].classList.add('active');
    captionDots[current].classList.add('active');
    renderCaption(current);
  }
  function startAuto(){
    clearInterval(sliderTimer);
    sliderTimer = setInterval(() => goTo(current + 1), 5000);
  }
  document.querySelector('.slide-prev').addEventListener('click', () => { goTo(current - 1); if(autoplayOn) startAuto(); });
  document.querySelector('.slide-next').addEventListener('click', () => { goTo(current + 1); if(autoplayOn) startAuto(); });
  applyAutoplayState();
 
  /* ---------- Member "View Info" modal ---------- */
  const infoOverlay = document.getElementById('infoModalOverlay');
  const infoClose = document.getElementById('infoModalClose');
  const infoPhotoWrap = document.getElementById('infoModalPhotoWrap');
  const infoName = document.getElementById('infoModalName');
  const infoRole = document.getElementById('infoModalRole');
  const infoBio = document.getElementById('infoModalBio');
  const infoBioSection = document.getElementById('infoModalBioSection');
  const infoSkills = document.getElementById('infoModalSkills');
  const infoSkillsSection = document.getElementById('infoModalSkillsSection');
  const infoEmail = document.getElementById('infoModalEmail');
  const infoEmailSection = document.getElementById('infoModalEmailSection');

  function openInfoModal(data){
    infoName.textContent = data.name || '';
    infoRole.textContent = data.role || '';

    infoPhotoWrap.innerHTML = data.photo
      ? `<img src="${data.photo}" alt="${data.name} photo">`
      : `<div class="avatar-fallback">${data.initials || ''}</div>`;

    if(data.bio){
      infoBio.textContent = data.bio;
      infoBioSection.style.display = '';
    } else {
      infoBioSection.style.display = 'none';
    }

    infoSkills.innerHTML = '';
    if(Array.isArray(data.skills) && data.skills.length){
      data.skills.forEach(s => {
        const span = document.createElement('span');
        span.textContent = s;
        infoSkills.appendChild(span);
      });
      infoSkillsSection.style.display = '';
    } else {
      infoSkillsSection.style.display = 'none';
    }

    if(data.email){
      infoEmail.textContent = data.email;
      infoEmailSection.style.display = '';
    } else {
      infoEmailSection.style.display = 'none';
    }

    infoOverlay.classList.add('open');
  }
  function closeInfoModal(){ infoOverlay.classList.remove('open'); }

  document.querySelectorAll('.view-info-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      try{
        const data = JSON.parse(btn.dataset.member);
        openInfoModal(data);
      }catch(e){ console.error('Could not read member data', e); }
    });
  });
  infoClose.addEventListener('click', closeInfoModal);
  infoOverlay.addEventListener('click', (e) => { if(e.target === infoOverlay) closeInfoModal(); });
  document.addEventListener('keydown', (e) => { if(e.key === 'Escape') closeInfoModal(); });

  /* ---------- Member edit panels ---------- */
  const grid = document.getElementById('membersGrid');
  grid.querySelectorAll('[data-edit]').forEach(btn => {
    btn.addEventListener('click', () => {
      document.getElementById('edit-' + btn.dataset.edit).classList.toggle('open');
    });
  });
  grid.querySelectorAll('[data-cancel]').forEach(btn => {
    btn.addEventListener('click', () => {
      document.getElementById('edit-' + btn.dataset.cancel).classList.remove('open');
    });
  });
 
  /* ---------- Login modal (actual login happens in login.php) ---------- */
  const overlay = document.getElementById('loginOverlay');
  const loginBtn = document.getElementById('loginBtn');
  const loginBtnMobile = document.getElementById('loginBtnMobile');
 
  function openModal(){
    if(!overlay) return;
    overlay.classList.add('open');
    const u = document.getElementById('loginUser');
    if(u) u.focus();
  }
  function closeModal(){ if(overlay) overlay.classList.remove('open'); }
 
  if(loginBtn) loginBtn.addEventListener('click', openModal);
  if(loginBtnMobile) loginBtnMobile.addEventListener('click', () => { toggleDrawer(false); openModal(); });
  const closeBtn = document.getElementById('closeModal');
  if(closeBtn) closeBtn.addEventListener('click', closeModal);
  if(overlay) overlay.addEventListener('click', (e) => { if(e.target === overlay) closeModal(); });
  document.addEventListener('keydown', (e) => { if(e.key === 'Escape') closeModal(); });
 
  /* ---------- Active nav link on scroll ---------- */
  const navLinks = document.querySelectorAll('nav.main-nav a');
  const sections = ['#top','#members','#group-info','#contact'].map(id => document.querySelector(id));
  window.addEventListener('scroll', () => {
    let idx = 0;
    sections.forEach((sec, i) => { if(sec && window.scrollY >= sec.offsetTop - 120) idx = i; });
    navLinks.forEach(l => l.classList.remove('active'));
    if(navLinks[idx]) navLinks[idx].classList.add('active');
  });
 
})();
</script>
</body>
</html>