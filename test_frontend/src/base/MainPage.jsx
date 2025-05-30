import React, { useState, useEffect } from 'react';
import { Outlet } from 'react-router-dom';

const MainPage = ({ children }) => {
  const [currentDate, setCurrentDate] = useState('');

  // Fonction pour formater la date
  useEffect(() => {
    const date = new Date().toLocaleDateString('fr-FR', {
      day: 'numeric', 
      month: 'short', 
      year: 'numeric'
    });
    setCurrentDate(date);
  }, []);

  // Fonction pour changer le thème
  const changeTheme = (theme) => {
    document.documentElement.setAttribute('data-theme', theme);
  };

  return (
    <div class="drawer lg:drawer-open h-screen">
      <input id="my-drawer" type="checkbox" class="drawer-toggle" />

      <div class="drawer-side min-h-full">
        <label for="my-drawer" class="drawer-overlay"></label>
        <div class="flex flex-col h-full bg-base-200">
          <!-- Profil utilisateur -->
          <div class="flex items-center p-4 justify-between bg-base-200">
            <div class="flex items-center gap-3">
              <div class="avatar">
                <div class="w-10 rounded-full">
                  <img src="https://picsum.photos/200" alt="Photo de profil" />
                </div>
              </div>
              <div>
                <div class="font-medium">Riley Carter</div>
                <div class="text-xs text-base-content/70">riley@email.com</div>
              </div>
            </div>
          </div>
          
          <!-- Menu navigation principal -->
          <ul class="menu p-4 w-full text-base-content space-y-2 border-t border-base-300">
            <li class="menu-title"><span>Menu</span></li>
            <li><a>Home</a></li>
            <li><a>About</a></li>
            <li><a>Contact</a></li>
          </ul>
          
          <!-- Espace flexible pour pousser le menu utilisateur vers le bas -->
          <div class="flex-grow"></div>
          
          <!-- Menu utilisateur en bas -->
          <ul class="menu p-4 w-full text-base-content space-y-2 mt-auto border-t border-base-300">
            <li class="menu-title"><span>Utilisateur</span></li>
            <li><a>
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
              </svg>
              Profil
            </a></li>
            <li><a>
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
              Paramètres
            </a></li>
            <li><a class="text-error">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
              </svg>
              Se déconnecter
            </a></li>
          </ul>
        </div>
      </div>

      <div class="drawer-content flex flex-col">
        <!-- Navbar -->
        <div class="navbar bg-base-100 px-4 shadow-sm">

          <div class="flex-none lg:hidden">
            <label for="my-drawer" class="btn btn-square btn-ghost">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                   viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M4 6h16M4 12h16M4 18h16"/>
              </svg>
            </label>
          </div>
          
          <div class="navbar-start">
            <div class="breadcrumbs">
              <ul>
                <li><a>Home</a></li>
                <li>Dashboard</li>
              </ul>
            </div>
          </div>

          <div class="navbar-center">
            <span id="" class="text-sm">LBDR</span>
          </div>

          <div class="navbar-end gap-2">
            <!-- Date avec icône calendrier -->
            <div class="border border-base-300 rounded-lg px-3 py-1 flex items-center gap-2">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 9.75v7.5" />
              </svg>
              <span id="current-date" class="text-sm"></span>
            </div>

            <!-- Bouton notifications avec indicateur -->

            
            <div class="indicator">
              <span class="indicator-item badge badge-xs badge-secondary"></span> 
              <button class="btn btn-ghost btn-circle">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                </svg>
              </button>
            </div>

            <!-- Bouton changement de thème -->
            <div class="dropdown dropdown-end">
              <button tabindex="0" role="button" class="btn btn-ghost btn-circle">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" />
                </svg>
              </button>
              <ul tabindex="0" class="dropdown-content z-10 menu p-2 shadow bg-base-100 rounded-box w-52">
                <li><a data-set-theme="light">Light</a></li>
                <li><a data-set-theme="dark">Dark</a></li>
                <li><a data-set-theme="cupcake">Cupcake</a></li>
                <li><a data-set-theme="bumblebee">Bumblebee</a></li>
                <li><a data-set-theme="caramellatte">Caramellatte</a></li>
              </ul>
            </div>
          </div>
        </div>

        <!-- Main content -->
        <main class="p-4 flex-1 bg-base-100" id="root">
            {children}
            <Outlet />
        </main>
      </div>

    </div>
  );
};

export default MainPage;