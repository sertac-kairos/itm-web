/*
Template Name: Adminto - Responsive Bootstrap 5 Admin Dashboard
Author: Coderthemes
Website: https://coderthemes.com/
Contact: support@coderthemes.com
File: Articles Quilljs init js
*/
import Quill from 'quill'

// Initialize Quill editors for articles
document.addEventListener('DOMContentLoaded', function() {
    console.log('Articles Quill.js component loaded');
    
    // Check if we're on articles pages
    const articleEditors = document.querySelectorAll('[id^="content_editor_"]');
    console.log('Found article editors:', articleEditors.length);
    
    if (articleEditors.length > 0) {
        // Get locales from global variable
        const locales = window.articleLocales || ['tr', 'en'];
        console.log('Locales:', locales);
        const quillEditors = {};
        
        // Initialize Quill editors
        locales.forEach(function(locale) {
            const editorId = 'content_editor_' + locale;
            const textareaId = 'content_' + locale;
            const editorElement = document.getElementById(editorId);
            
            console.log('Initializing editor for locale:', locale, 'Element:', editorElement);
            
            if (editorElement) {
                try {
                    // Clear loading content
                    editorElement.innerHTML = '';
                    
                    quillEditors[locale] = new Quill('#' + editorId, {
                        theme: 'snow',
                        modules: {
                            toolbar: [
                                [{ 'font': [] }, { 'size': [] }],
                                ['bold', 'italic', 'underline', 'strike'],
                                [{ 'color': [] }, { 'background': [] }],
                                [{ 'script': 'super' }, { 'script': 'sub' }],
                                [{ 'header': [false, 1, 2, 3, 4, 5, 6] }, 'blockquote', 'code-block'],
                                [{ 'list': 'ordered' }, { 'list': 'bullet' }, { 'indent': '-1' }, { 'indent': '+1' }],
                                ['direction', { 'align': [] }],
                                ['link', 'image', 'video'],
                                ['clean']
                            ]
                        },
                        placeholder: locale === 'tr' ? 'Türkçe içerik girin...' : 'Enter content in ' + locale.toUpperCase() + '...'
                    });
                    
                    console.log('Quill editor created for locale:', locale);
                    
                    // Load existing content if any
                    const textarea = document.getElementById(textareaId);
                    if (textarea && textarea.value) {
                        quillEditors[locale].root.innerHTML = textarea.value;
                    }
                    
                    // Update textarea when editor content changes
                    quillEditors[locale].on('text-change', function() {
                        if (textarea) {
                            textarea.value = quillEditors[locale].root.innerHTML;
                        }
                    });
                } catch (error) {
                    console.error('Error creating Quill editor for locale:', locale, error);
                    editorElement.innerHTML = '<div style="padding: 20px; color: red;">Editör yüklenirken hata oluştu: ' + error.message + '</div>';
                }
            }
        });
        
        // Handle form submission
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', function(e) {
                console.log('Form submitting, updating textareas');
                // Update all textareas with editor content before submission
                locales.forEach(function(locale) {
                    const textarea = document.getElementById('content_' + locale);
                    if (quillEditors[locale] && textarea) {
                        textarea.value = quillEditors[locale].root.innerHTML;
                        console.log('Updated textarea for locale:', locale);
                    }
                });
            });
        }
        
        // Make editors globally accessible for debugging
        window.quillEditors = quillEditors;
        console.log('Quill editors initialized:', Object.keys(quillEditors));
    }
});
