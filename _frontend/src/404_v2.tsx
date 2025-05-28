// src/pages/NotFound404.tsx
import React, { useRef, useState, useEffect, Suspense } from 'react';
import { Box, Typography, Container } from '@mui/material';
import { Canvas, useFrame } from '@react-three/fiber';
import { Icosahedron } from '@react-three/drei';

export default function NotFound404_v2() {
  const [targetRot, setTargetRot] = useState<{ x: number; y: number; z: number }>({
    x: 0,
    y: 0,
    z: 0,
  });
  const [rolling, setRolling] = useState(false);
  const isMobile =
    typeof window !== 'undefined' && 'DeviceMotionEvent' in window;

  useEffect(() => {
    if (!isMobile) return;

    let lastTime = 0;
    const THRESHOLD = 15; // m/s², à ajuster selon sensibilité
    const SHAKE_COOLDOWN = 1000; // ms

    const handleMotion = (e: DeviceMotionEvent) => {
      const now = Date.now();
      if (now - lastTime < SHAKE_COOLDOWN) return;

      const acc = e.accelerationIncludingGravity;
      if (!acc) return;

      const totalAcc = Math.sqrt(
        (acc.x ?? 0) ** 2 +
        (acc.y ?? 0) ** 2 +
        (acc.z ?? 0) ** 2
      );

      if (totalAcc > THRESHOLD) {
        lastTime = now;
        triggerRoll();
      }
    };

    // iOS 13+ : demander la permission
    if ((DeviceMotionEvent as any).requestPermission) {
      ;(DeviceMotionEvent as any)
        .requestPermission()
        .then((perm: string) => {
          if (perm === 'granted') {
            window.addEventListener('devicemotion', handleMotion);
          }
        })
        .catch(console.error);
    } else {
      window.addEventListener('devicemotion', handleMotion);
    }

    return () => {
      window.removeEventListener('devicemotion', handleMotion);
    };
  }, [isMobile]);

  const triggerRoll = () => {
    if (rolling) return;
    setRolling(true);

    // génère une rotation aléatoire pour simuler le roll
    const newRot = {
      x: Math.random() * Math.PI * 2,
      y: Math.random() * Math.PI * 2,
      z: Math.random() * Math.PI * 2,
    };
    setTargetRot(newRot);

    // après 1s, on autorise un nouveau shake
    setTimeout(() => setRolling(false), 1000);
  };

  return (
    <Container
      sx={{
        height: '100vh',
        display: 'flex',
        flexDirection: 'column',
        alignItems: 'center',
        justifyContent: 'center',
        bgcolor: 'background.default',
        px: 2,
        textAlign: 'center',
      }}
    >
      <Typography variant="h4" gutterBottom>
        404 — Page non trouvée
      </Typography>
      <Typography variant="body1" mb={2}>
        Secouez votre téléphone pour lancer le dé ! 🎲
      </Typography>
      <Box sx={{ width: 300, height: 300, touchAction: 'none' }}>
        <Canvas camera={{ position: [0, 0, 5], fov: 50 }}>
          <ambientLight intensity={0.6} />
          <directionalLight position={[5, 5, 5]} intensity={1} />
          <Suspense fallback={null}>
            <D20 rotation={targetRot} />
          </Suspense>
        </Canvas>
      </Box>
    </Container>
  );
}

function D20({
  rotation,
}: {
  rotation: { x: number; y: number; z: number };
}) {
  const mesh = useRef<THREE.Mesh>(null!);

  useFrame((_, delta) => {
    // interpolation fluide vers la rotation cible
    mesh.current.rotation.x += (rotation.x - mesh.current.rotation.x) * 5 * delta;
    mesh.current.rotation.y += (rotation.y - mesh.current.rotation.y) * 5 * delta;
    mesh.current.rotation.z += (rotation.z - mesh.current.rotation.z) * 5 * delta;
  });

  return (
    <mesh ref={mesh}>
      <Icosahedron args={[1, 0]}>
        <meshStandardMaterial flatShading color="#1976d2" />
      </Icosahedron>
      {/* 
        Pour afficher les chiffres du D20 :
        - Charger un modèle GLTF texturé via useGLTF
        - Ou superposer des TextGeometry sur chaque face
        Exemple plus avancé hors scope de cet exemple minimal.
      */}
    </mesh>
  );
}
