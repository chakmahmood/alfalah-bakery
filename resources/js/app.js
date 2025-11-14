import './bootstrap';
import { greet } from './kasir-app';

// Buat global
window.greet = greet;

// Optional: test
console.log(window.greet("Al Foz"));

