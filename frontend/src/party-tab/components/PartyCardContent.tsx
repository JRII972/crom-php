import {
	FC
} from 'react';
import {
	CardContent,
	Typography, useMediaQuery,
	useTheme,
	Stack,
	Grid
} from '@mui/material';
import GameSession from '../../types/GameSession';
import { playerNumber, trimString } from '../../utils/utils';
import PlayersDisplay from './PlayersDisplay';
import { findGameByName } from '../data/games';


interface PartyCardProps {
  partie: GameSession;
}


// TODO: FIX card size adjust

export const PartyCardContent: FC<{ 
  partie: GameSession;
	cardMinWidth: number;
	displayDate: boolean;
  type?: 'session' | 'game' | 'party'}> = ({ partie, cardMinWidth, type='session', displayDate=false}) => {

    
	const theme = useTheme();
	const isMobileScreen = useMediaQuery(theme.breakpoints.down('md'));

  const cardImageSize = isMobileScreen ? 100 : 140;
  const titleSize = isMobileScreen ? '1em' : '1.15em';
  const subTitleSize = isMobileScreen ? '0.8em' : '0.9em';
  const subTitleLineHeight = isMobileScreen ? '0.8em' : '0.9em';
  const commentSize = isMobileScreen ? '0.75em' : '0.85em';
  const playerListPadding = isMobileScreen ? 0 : 0;

	const isSession = (type == 'session')
	const isGame = (type == 'game')
	const isParty = (type == 'party')

	const gameData = findGameByName(partie.jeu)
  
  const dateFormatingOption = partie.maitre_de_jeu.length < 11 ? {
    weekday: 'short',
    day: 'numeric',
    month: 'numeric',
    } : { day: 'numeric', month: 'numeric' }
  							
  const partieDate = new Date(Date.parse(partie.date)).toLocaleDateString('fr-FR', dateFormatingOption)
									
  
 return(
    <CardContent
			sx={{
				backgroundColor: 'background.card',
				p: 1,
				
				flex: 1,
				// boxSizing: 'border-box',
				width: '100%',
				display: 'flex',
				flexDirection: 'column',
				justifyContent: 'space-between',
			}}
		>
		<Stack spacing={0.2} >
				<Stack sx={{  }}>
					<Typography variant="h5" component="div" 
							sx={{
									textalign: 'center',
									alignItems: 'center',
									fontSize: titleSize,
									whiteSpace: 'nowrap',
									textAlign: 'center'
							}}
					>
							{partie.party_name}
					</Typography>
					
					{isSession &&
					<>
						<Stack direction={"column"} spacing={0.5} sx={{alignItems: 'center', justifyContent: 'space-between' }}>
								<Typography gutterBottom variant="subtitle1" component="div" 
								sx={{
										fontSize: subTitleSize,
										lineHeight: subTitleLineHeight,
										alignItems: 'center',
								}}
								>
								{gameData ? gameData.displayName : partie.jeu}
								</Typography>

								<Typography gutterBottom variant="subtitle1" component="div" 
								sx={{
										fontSize: subTitleSize,
										lineHeight: subTitleLineHeight,
										alignItems: 'center',
								}}
								>
								{!displayDate && <>{partie.maitre_de_jeu}</>}
								{displayDate && <>{trimString(partie.maitre_de_jeu, 17, 16, '.')} | {partieDate}</>}
								</Typography>
						</Stack>

						<Grid container spacing={1} sx={{mt: 0.7}} >

								<Grid size={4}
								sx={{
										display: 'flex',
										justifyContent: 'left',
										alignItems: 'center',
								}}
								>
								<Typography variant="subtitle1" component="div" 
										sx={{
												fontSize: subTitleSize,
												lineHeight: subTitleLineHeight,
												alignItems: 'center',
										}}
										>
										{partie.type}
								</Typography>
								</Grid>

								<Grid size={4}                  
									sx={{
											display: 'flex',
											justifyContent: 'center',
											alignItems: 'center',
									}}
								>
									{playerNumber(partie, subTitleSize, isMobileScreen)}
								</Grid>

								<Grid size={4} 
								sx={{
										display: 'flex',
										justifyContent: 'right',
										alignItems: 'center',
								}}
								>
								<Typography variant="subtitle1" component="div" 
										sx={{
												fontSize: subTitleSize,
												lineHeight: subTitleLineHeight,
												alignItems: 'center',
										}}
										>
										{partie.lieu}
								</Typography>                  
								</Grid>

								
						</Grid>
					</>
					}

					{isGame && 					
						<Stack direction={"row"} spacing={0.5} sx={{alignItems: 'center', justifyContent: 'space-between', my: 0.5 }}>
							<Typography variant="subtitle1" component="div" 
								sx={{
										fontSize: subTitleSize,
										lineHeight: subTitleLineHeight,
										alignItems: 'center',
								}}
								>
								{gameData ? gameData.displayName : partie.jeu}
							</Typography>

							<Typography variant="subtitle1" component="div" 
								sx={{
										fontSize: subTitleSize,
										lineHeight: subTitleLineHeight,
										alignItems: 'center',
								}}
								>
								
								{playerNumber(partie, subTitleSize, isMobileScreen)}
							</Typography>
						</Stack>
					}




				</Stack>

				{/* <PartyCardSubInfo partie={partie} commentSize={commentSize} isMobileScreen={isMobileScreen}/> */}
				
				<Typography
					variant="body2"
					sx={{
							color: 'text.secondary',
							display: '-webkit-box',
							WebkitBoxOrient: 'vertical',
							WebkitLineClamp: 3,
							overflow: 'hidden',
							textOverflow: 'ellipsis',
							fontSize: commentSize,
					}}
				>
					{partie.short_coment}
				</Typography>
		</Stack>
		<PlayersDisplay
			players={partie.players}
			maxWidth={cardMinWidth - 20} // Largeur maximale en pixels
			spaceWidth={5} // Espace avant le séparateur en pixels
			separator=", " // Séparateur entre les noms
			fontSize={commentSize} // Taille de la police
		/>
	</CardContent>
  );
  
}