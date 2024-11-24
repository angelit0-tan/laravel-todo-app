export const fileTypes = [
    'image/png',
    'image/jpeg',
    'image/svg+xml',
    'video/mp4',
    'text/csv',
    'application/msword',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
];

export const isValidFile = (file) => fileTypes.includes(file.type);

export default null;
  