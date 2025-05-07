// src/pages/NotFound404.tsx
import React, { useRef, useState, useEffect, Suspense } from 'react';
import { Box, Typography, Container } from '@mui/material';
import { Canvas, useFrame } from '@react-three/fiber';
import { Icosahedron } from '@react-three/drei';

export default function NotFound404() {
  // état pour la rotation cible du dé
  const [targetRot, setTargetRot] = useState<{ x: number; y: number }>({ x: 0, y: 0 });

  // détecter si on est sur mobile
  const isMobile = typeof window !== 'undefined' && 'DeviceOrientationEvent' in window;

  // souris : mapping du déplacement relatif au centre → rotation
  useEffect(() => {
    if (!isMobile) {
      const handleMouse = (e: MouseEvent) => {
        const cx = window.innerWidth / 2;
        const cy = window.innerHeight / 2;
        const dx = (e.clientX - cx) / cx;  // -1 → 1
        const dy = (e.clientY - cy) / cy;  // -1 → 1
        setTargetRot({ x: dy * 0.5, y: dx * 0.5 });
      };
      window.addEventListener('mousemove', handleMouse);
      return () => window.removeEventListener('mousemove', handleMouse);
    }
  }, [isMobile]);

  // gyroscope : mapping beta/gamma → rotation
  useEffect(() => {
    if (isMobile) {
      const handleOrient = (ev: DeviceOrientationEvent) => {
        // gamma : left/right  → rotation y
        // beta  : front/back → rotation x
        const gyroY = (ev.gamma ?? 0) * (Math.PI / 180) * 0.5;
        const gyroX = (ev.beta  ?? 0) * (Math.PI / 180) * 0.5;
        setTargetRot({ x: gyroX, y: gyroY });
      };
      // iOS : demander la permission si nécessaire
      if ((DeviceOrientationEvent as any).requestPermission) {
        (DeviceOrientationEvent as any).requestPermission().then((perm: string) => {
          if (perm === 'granted') window.addEventListener('deviceorientation', handleOrient);
        });
      } else {
        window.addEventListener('deviceorientation', handleOrient);
      }
      return () => window.removeEventListener('deviceorientation', handleOrient);
    }
  }, [isMobile]);

  useEffect(() => {
    if (isMobile && (DeviceOrientationEvent as any).requestPermission) {
      (DeviceOrientationEvent as any)
        .requestPermission()
        .then((perm: string) => {
          if (perm === 'granted')
            window.addEventListener('deviceorientation', handleOrient);
        })
        .catch(console.error);
    }
  }, [isMobile]);

  return (
    <Container
      sx={{
        height: '100vh',
        display: 'flex', flexDirection: 'column',
        alignItems: 'center', justifyContent: 'center',
        background: theme => theme.palette.background.default,
      }}
    >
      <Typography variant="h3" gutterBottom>
        404 – Page introuvable
      </Typography>

      <Box
        sx={{
          width: 300, height: 300,
          cursor: isMobile ? 'grab' : 'none',
        }}
      >
        <Canvas camera={{ position: [0, 0, 5], fov: 50 }}>
          <ambientLight intensity={0.5} />
          <directionalLight position={[5, 5, 5]} intensity={1} />
          <Suspense fallback={null}>
            <D20 rotation={targetRot} />
          </Suspense>
        </Canvas>
      </Box>
    </Container>
  );
}

// Composant du D20
function D20({ rotation }: { rotation: { x: number; y: number } }) {
  const ref = useRef<THREE.Mesh>(null!);

  // animer la rotation vers la cible de façon fluide
  useFrame((_, delta) => {
    ref.current.rotation.x += (rotation.x - ref.current.rotation.x) * 4 * delta;
    ref.current.rotation.y += (rotation.y - ref.current.rotation.y) * 4 * delta;
  });

  return (
    <mesh ref={ref}>
      {/* Icosaèdre (D20) */}
      <Icosahedron args={[1, 0]}>
        <meshStandardMaterial color="#2196f3" flatShading />
      </Icosahedron>
      {/* Pour ajouter les chiffres, il faudrait un shader ou un modèle GLTF texturé */}
    </mesh>
  );
}
