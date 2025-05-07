import Table from '@mui/material/Table';
import { styled } from '@mui/material/styles';
import TableRow from '@mui/material/TableRow';
import TableBody from '@mui/material/TableBody';
import TableHead from '@mui/material/TableHead';
import TableCell, { tableCellClasses } from '@mui/material/TableCell';

const StyledTableCell = styled(TableCell)(({ theme }) => ({
  [`&.${tableCellClasses.head}`]: {
    backgroundColor: theme.palette.common.black,
    color: theme.palette.common.white,
  },
  [`&.${tableCellClasses.body}`]: {
    fontSize: 14,
  },
}));

function chunkArray(array, chunkSize) {
  const result = [];
  for (let i = 0; i < array.length; i += chunkSize) {
    result.push(array.slice(i, i + chunkSize));
  }
  return result;
}

export default function PlayerTable({row, nbr=3}) {
    const players = chunkArray(row.players, nbr); 

    

    return(
      <Table>
        <TableHead><TableRow><StyledTableCell colSpan={nbr}>Joueurs inscrits</StyledTableCell></TableRow></TableHead>
        <TableBody>
          {players.map((row, rowIndex) => (
            <TableRow key={rowIndex}>
              {/* une cellule par nom */}
              {row.map((name, cellIndex) => (
                <TableCell key={cellIndex}>{name}</TableCell>
              ))}
              {/* si moins de 3 noms sur la dernière ligne, on complète avec des cellules vides */}
              {row.length < 3 &&
                Array.from({ length: 3 - row.length }).map((_, emptyIndex) => (
                  <TableCell key={`empty-${emptyIndex}`} />
                ))
              }
            </TableRow>
          ))}
        </TableBody>
      </Table>
      )
  }