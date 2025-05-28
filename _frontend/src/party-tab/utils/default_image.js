
export function default_image(type = 'CAMPAGNE') {
    const JDR_IMAGE = [
        '/data/images/default/default_jdr_image_1.webp',
        '/data/images/default/default_jdr_image_2.webp',
        '/data/images/default/default_jdr_image_3.jpg',
        '/data/images/default/default_jdr_image_4.jpg',
    ]
    const TABLETOP_IMAGE = [
        '/data/images/default/default_jdr_image_1.webp'
    ]
    if (type == 'CAMPAGNE' ) {
        return JDR_IMAGE[Math.floor(Math.random()*JDR_IMAGE.length)];
    }
    if (type == 'ONESHOT' ) {
        return JDR_IMAGE[Math.floor(Math.random()*JDR_IMAGE.length)];
    }
    if (type == 'EVENEMENT' ) {
        return JDR_IMAGE[Math.floor(Math.random()*JDR_IMAGE.length)];
    }
    if (type == 'JEU_DE_SOCIETE') {
        return JDR_IMAGE[Math.floor(Math.random()*JDR_IMAGE.length)];
    }
    
    return JDR_IMAGE[Math.floor(Math.random()*JDR_IMAGE.length)];

}