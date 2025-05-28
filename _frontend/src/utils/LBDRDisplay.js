
import { brand } from '../shared-theme/themePrimitives'

export function DisplayLBDR() {
    console.info(
       `%c
██╗     ██████╗ ██████╗ ██████╗ 
██║     ██╔══██╗██╔══██╗██╔══██╗
██║     ██████╔╝██║  ██║██████╔╝
██║     ██╔══██╗██║  ██║██╔══██╗
███████╗██████╔╝██████╔╝██║  ██║
╚══════╝╚═════╝ ╚═════╝ ╚═╝  ╚═╝      

Site de présentation pour l\' association Les Batisseurs de rêve                
`, 
`color: ${brand[700]}`,
    )
}