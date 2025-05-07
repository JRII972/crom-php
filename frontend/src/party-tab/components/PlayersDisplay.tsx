import React, { useEffect, useState } from 'react';
import { Stack, Typography } from '@mui/material';

// Types
interface PlayerWithWidth {
  player: string;
  width: number;
}

interface Line {
  players: string[];
}

interface PlayersDisplayProps {
  players: string[];
  maxWidth: number; // Largeur maximale en pixels
  spaceWidth?: number; // Largeur de l'espace entre les noms en pixels (avant le séparateur)
  separator?: string; // Séparateur entre les noms (ex. ", ", "- ")
}

// Utilitaire pour mesurer la largeur d'un texte en pixels
const getTextWidth = (text: string): number => {
  const canvas = document.createElement('canvas');
  const context = canvas.getContext('2d');
  if (!context) return text.length * 10; // Fallback si canvas non disponible

  // Simuler la police de MUI pour variant="overline"
  // Typiquement : font-family: Roboto, font-size: 12px, text-transform: uppercase
  context.font = '12px Roboto';
  context.textTransform = 'uppercase';
  return context.measureText(text.toUpperCase()).width;
};

// Algorithme de programmation dynamique pour organiser les joueurs
const organizePlayersDynamic = (
  players: string[],
  maxWidth: number,
  spaceWidth: number,
  separator: string
): Line[] => {
  const n = players.length;
  if (n === 0) return [];

  // Calculer les largeurs des joueurs et du séparateur
  const playerWidths: PlayerWithWidth[] = players.map(player => ({
    player,
    width: getTextWidth(player),
  }));
  const separatorWidth = getTextWidth(separator);

  // Vérifier si un nom est trop long
  for (const { player, width } of playerWidths) {
    if (width > maxWidth) {
      console.warn(`Le joueur "${player}" (largeur ${width}px) dépasse la largeur maximale ${maxWidth}px.`);
    }
  }

  // Tableau DP : dp[used] = nombre minimal de lignes pour les joueurs utilisés
  const dp = new Array(1 << n).fill(Infinity);
  dp[0] = 0; // Aucun joueur utilisé -> 0 ligne

  // Stocker les transitions et les lignes utilisées
  const transitions = new Array(1 << n).fill(null);
  const usedLines = new Array(1 << n).fill(null);

  // Parcourir tous les états (masques de bits)
  for (let used = 0; used < (1 << n); used++) {
    if (dp[used] === Infinity) continue;

    // Trouver les joueurs non utilisés
    const available: number[] = [];
    for (let i = 0; i < n; i++) {
      if (!(used & (1 << i))) available.push(i);
    }

    // Tester tous les sous-ensembles de joueurs disponibles pour former une ligne
    for (let subset = 1; subset < (1 << available.length); subset++) {
      let lineWidth = 0;
      let linePlayers: number[] = [];
      let newUsed = used;

      // Calculer la largeur du sous-ensemble
      for (let j = 0; j < available.length; j++) {
        if (subset & (1 << j)) {
          const playerIndex = available[j];
          linePlayers.push(playerIndex);
          lineWidth += playerWidths[playerIndex].width;
          if (linePlayers.length > 1) lineWidth += spaceWidth + separatorWidth;
          newUsed |= 1 << playerIndex;
        }
      }

      // Vérifier si la ligne est valide
      if (lineWidth <= maxWidth && dp[newUsed] > dp[used] + 1) {
        dp[newUsed] = dp[used] + 1;
        transitions[newUsed] = used;
        usedLines[newUsed] = linePlayers;
      }
    }
  }

  // Reconstruire la solution
  const lines: Line[] = [];
  let currentUsed = (1 << n) - 1; // Tous les joueurs utilisés
  while (currentUsed !== 0) {
    const prevUsed = transitions[currentUsed];
    const lineIndices = usedLines[currentUsed];
    const line: Line = {
      players: lineIndices.map((index: number) => playerWidths[index].player),
    };
    lines.push(line);
    currentUsed = prevUsed;
  }

  return lines;
};

// Composant React/MUI
const PlayersDisplay: React.FC<PlayersDisplayProps> = ({
  players,
  maxWidth,
  spaceWidth = 5,
  separator = ', ',
}) => {
  const [lines, setLines] = useState<Line[]>([]);

  useEffect(() => {
    try {
      const organizedLines = organizePlayersDynamic(players, maxWidth, spaceWidth, separator);
      setLines(organizedLines);
    } catch (error) {
      console.error('Erreur lors de l’organisation des joueurs :', error);
      setLines([]);
    }
  }, [players, maxWidth, spaceWidth, separator]);

  return (
    <Stack className="players-container" style={{ width: `${maxWidth}px` }}>
      {lines.length === 0 && (
        <Typography variant="overline" sx={{ lineHeight: 1.2, textAlign: 'center' }}>
          Aucun joueur
        </Typography>
      )}
      {lines.map((line, index) => (
        <Typography
          key={index}
          variant="overline"
          sx={{ lineHeight: 1.2, textAlign: 'center', whiteSpace: 'nowrap' }}
        >
          {line.players.join(separator)}
        </Typography>
      ))}
    </Stack>
  );
};

// CSS intégré pour le style
const styles = `
  .players-container {
    box-sizing: border-box;
    font-family: Roboto, sans-serif;
  }
`;

// Injecter les styles dans le DOM
const styleSheet = document.createElement('style');
styleSheet.textContent = styles;
document.head.appendChild(styleSheet);

export default PlayersDisplay;