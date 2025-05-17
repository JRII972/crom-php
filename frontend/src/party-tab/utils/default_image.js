
export function default_image(type = 'JDR') {
    const JDR_IMAGE = [
        '/data/images/default_jdr_image_1.webp'
    ]
    const TABLETOP_IMAGE = [
        '/data/images/default_jdr_image_1.webp'
    ]
    if (type == 'JR') {
        return JDR_IMAGE[Math.floor(Math.random()*JDR_IMAGE.length)];
    }
    if (type == 'TABLETOP_IMAGE') {
        return JDR_IMAGE[Math.floor(Math.random()*JDR_IMAGE.length)];
    }
    
    return JDR_IMAGE[Math.floor(Math.random()*JDR_IMAGE.length)];

}