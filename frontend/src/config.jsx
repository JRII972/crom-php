import { useMediaQuery } from '@mui/material'

export const isMobile = () => { return  useMediaQuery('(max-width:900px)')};