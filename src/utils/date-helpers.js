import { format, parseISO } from 'date-fns';

export function formatDate(date, formatString = 'PPP') {
  if (typeof date === 'string') {
    date = parseISO(date);
  }
  return format(date, formatString);
}

export function formatTime(date, formatString = 'p') {
  if (typeof date === 'string') {
    date = parseISO(date);
  }
  return format(date, formatString);
}