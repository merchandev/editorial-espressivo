export const calculateReadability = (htmlContent) => {
    if (!htmlContent) return 0;

    // Limpiar HTML para contar solo texto real
    const text = htmlContent.replace(/<[^>]*>?/gm, '');
    const words = text.trim().split(/\s+/).filter(w => w.length > 0);
    
    if (words.length === 0) return 0;

    // Lógica simulada: Mientras más palabras, sube el puntaje hasta un óptimo.
    // En producción, aquí implementarías la fórmula de Flesch-Szigriszt.
    let score = Math.min((words.length / 300) * 100, 100);
    
    return Math.round(score);
};
