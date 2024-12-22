export function showModal(modal) {
    if (!modal) return; // If the modal element is not provided, exit the function
    
    modal.style.display = 'block'; // Set the display style of the modal to 'block' to make it visible
    modal.dispatchEvent(new Event('show')); // Dispatch a custom 'show' event on the modal element
    
    document.body.style.overflow = 'hidden'; // Prevent scrolling on the main page by setting the body's overflow style to 'hidden'
}
  
  export function hideModal(modal) {
    if (!modal) return;
    
    modal.style.display = 'none';
    modal.dispatchEvent(new Event('hide'));
    
    document.body.style.overflow = '';
  }