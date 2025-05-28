import React, { useEffect, useState } from 'react';

// Types
interface WordWithWidth {
  word: string;
  width: number;
}

interface Line {
  words: string[];
}

// Props du composant
interface WordDisplayProps {
  words: string[];
  maxWidth: number; // Largeur maximale en pixels
  font?: string; // Police pour mesurer les largeurs (ex. "16px Arial")
  spaceWidth?: number; // Largeur de l'espace entre les mots en pixels
}

// Utilitaire pour mesurer la largeur d'un mot en pixels
const getWordWidth = (word: string, font: string): number => {
  const canvas = document.createElement('canvas');
  const context = canvas.getContext('2d');
  if (!context) return word.length * 10; // Fallback si canvas non disponible
  context.font = font;
  return context.measureText(word).width;
};

// Algorithme de programmation dynamique pour organiser les mots
const organizeWordsDynamic = (
  words: string[],
  maxWidth: number,
  font: string,
  spaceWidth: number
): Line[] => {
  const n = words.length;
  if (n === 0) return [];

  // Calculer les largeurs des mots
  const wordWidths: WordWithWidth[] = words.map(word => ({
    word,
    width: getWordWidth(word, font),
  }));

  // Vérifier si un mot est trop long
  for (const { word, width } of wordWidths) {
    if (width > maxWidth) {
      console.warn(`Le mot "${word}" (largeur ${width}px) dépasse la largeur maximale ${maxWidth}px.`);
    }
  }

  // Tableau DP : dp[used] = nombre minimal de lignes pour les mots utilisés
  const dp = new Array(1 << n).fill(Infinity);
  dp[0] = 0; // Aucun mot utilisé -> 0 ligne

  // Stocker les transitions et les lignes utilisées
  const transitions = new Array(1 << n).fill(null);
  const usedLines = new Array(1 << n).fill(null);

  // Parcourir tous les états (masques de bits)
  for (let used = 0; used < (1 << n); used++) {
    if (dp[used] === Infinity) continue;

    // Trouver les mots non utilisés
    const available: number[] = [];
    for (let i = 0; i < n; i++) {
      if (!(used & (1 << i))) available.push(i);
    }

    // Tester tous les sous-ensembles de mots disponibles pour former une ligne
    for (let subset = 1; subset < (1 << available.length); subset++) {
      let lineWidth = 0;
      let lineWords: number[] = [];
      let newUsed = used;

      // Calculer la largeur du sous-ensemble
      for (let j = 0; j < available.length; j++) {
        if (subset & (1 << j)) {
          const wordIndex = available[j];
          lineWords.push(wordIndex);
          lineWidth += wordWidths[wordIndex].width;
          if (lineWords.length > 1) lineWidth += spaceWidth;
          newUsed |= 1 << wordIndex;
        }
      }

      // Vérifier si la ligne est valide
      if (lineWidth <= maxWidth && dp[newUsed] > dp[used] + 1) {
        dp[newUsed] = dp[used] + 1;
        transitions[newUsed] = used;
        usedLines[newUsed] = lineWords;
      }
    }
  }

  // Reconstruire la solution
  const lines: Line[] = [];
  let currentUsed = (1 << n) - 1; // Tous les mots utilisés
  while (currentUsed !== 0) {
    const prevUsed = transitions[currentUsed];
    const lineIndices = usedLines[currentUsed];
    const line: Line = {
      words: lineIndices.map((index: number) => wordWidths[index].word),
    };
    lines.push(line);
    currentUsed = prevUsed;
  }

  return lines;
};

// Composant React
const WordDisplay: React.FC<WordDisplayProps> = ({
  words,
  maxWidth,
  font = '16px Arial',
  spaceWidth = 5,
}) => {
  const [lines, setLines] = useState<Line[]>([]);

  useEffect(() => {
    try {
      const organizedLines = organizeWordsDynamic(words, maxWidth, font, spaceWidth);
      setLines(organizedLines);
    } catch (error) {
      console.error('Erreur lors de l’organisation des mots :', error);
      setLines([]);
    }
  }, [words, maxWidth, font, spaceWidth]);

  return (
    <div className="word-container" style={{ width: `${maxWidth}px` }}>
      {lines.length === 0 && <p>Aucun mot à afficher.</p>}
      {lines.map((line, index) => (
        <p key={index} className="word-line">
          {line.words.join(' ')}
        </p>
      ))}
    </div>
  );
};

// CSS intégré pour le style
const styles = `
  .word-container {
    box-sizing: border-box;
    font-family: Arial, sans-serif;
  }
  .word-line {
    margin: 0;
    padding: 0;
    line-height: 1.5;
    white-space: nowrap; /* Éviter le retour à la ligne automatique */
  }
`;

// Injecter les styles dans le DOM
const styleSheet = document.createElement('style');
styleSheet.textContent = styles;
document.head.appendChild(styleSheet);

export default WordDisplay;