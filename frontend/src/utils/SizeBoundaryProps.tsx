import React, { useRef, useState, useEffect } from 'react';

interface SizeBoundaryProps {
  children: React.ReactNode;
  minWidth: number;
  maxWidth: number;
}

const SizeBoundary: React.FC<SizeBoundaryProps> = ({ children, minWidth, maxWidth }) => {
  const ref = useRef<HTMLDivElement>(null);
  const [isVisible, setIsVisible] = useState(true);

  useEffect(() => {
    const observer = new ResizeObserver((entries) => {
      for (const entry of entries) {
        const { width } = entry.contentRect;
        setIsVisible(width >= minWidth && width <= maxWidth);
      }
    });

    if (ref.current) {
      observer.observe(ref.current);
    }

    return () => {
      if (ref.current) {
        observer.unobserve(ref.current);
      }
    };
  }, [minWidth, maxWidth]);

  return (
    <div ref={ref} style={{ display: isVisible ? 'block' : 'none', wi, }}>
      {children}
    </div>
  );
};

export default SizeBoundary;