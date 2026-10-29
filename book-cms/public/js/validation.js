/**
 * LibrisCMS - Form Validation & Algorithmic Utilities
 * Demonstrates Syllabus Practicals (Regex Validation, Array Search & String Operations)
 */

document.addEventListener('DOMContentLoaded', () => {
  initReviewFormValidation();
  initBookFormValidation();
});

/* ==========================================================================
   1. Client-Side Review Form Validation
   Demonstrating Practical 1 rules:
   - Email format checking (@ and .)
   - Name and rating presence
   ========================================================================== */
function initReviewFormValidation() {
  const form = document.getElementById('review-form');
  if (!form) return;

  form.addEventListener('submit', (e) => {
    const nameInput = document.getElementById('review-user-name');
    const emailInput = document.getElementById('review-user-email');
    const commentInput = document.getElementById('review-comment');

    let isValid = true;
    let errorMessage = '';

    if (!nameInput.value.trim()) {
      isValid = false;
      errorMessage = 'Please enter your name.';
      nameInput.focus();
    } else if (emailInput.value.trim() && !validateEmail(emailInput.value.trim())) {
      isValid = false;
      errorMessage = 'Please enter a valid email address (must contain @ and . domain).';
      emailInput.focus();
    } else if (!commentInput.value.trim() || commentInput.value.trim().length < 5) {
      isValid = false;
      errorMessage = 'Review comment must be at least 5 characters long.';
      commentInput.focus();
    }

    if (!isValid) {
      e.preventDefault();
      alert(errorMessage);
    }
  });
}

/* ==========================================================================
   2. Book Editor Form Validation
   - ISBN format validation
   - Year range bounds (1800 - 2099)
   - Price non-negative
   ========================================================================== */
function initBookFormValidation() {
  const form = document.getElementById('book-editor-form');
  if (!form) return;

  form.addEventListener('submit', (e) => {
    const isbn = document.getElementById('book-isbn').value.trim();
    const year = parseInt(document.getElementById('book-year').value, 10);
    const price = parseFloat(document.getElementById('book-price').value || 0);

    const isbnPattern = /^(97[89]-?[0-9]{1,5}-?[0-9]{1,7}-?[0-9]{1,6}-?[0-9])|[0-9]{9}[0-9X]$/;

    if (!isbnPattern.test(isbn)) {
      if (!confirm('The entered ISBN does not conform strictly to standard ISBN-10 or ISBN-13 format. Do you still want to proceed?')) {
        e.preventDefault();
        return;
      }
    }

    if (isNaN(year) || year < 1800 || year > 2099) {
      e.preventDefault();
      alert('Publication year must be between 1800 and 2099.');
      return;
    }

    if (isNaN(price) || price < 0) {
      e.preventDefault();
      alert('Price must be a valid non-negative number.');
      return;
    }
  });
}

/* ==========================================================================
   3. Validation & Algorithm Helper Utilities
   ========================================================================== */

/**
 * Validates email address (checking @ and .)
 * Practical 1 requirement
 */
function validateEmail(email) {
  const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return re.test(email);
}

/**
 * Validates mobile number starting with 9, 8, 7, or 6
 * Practical 1 requirement
 */
function validateMobile(mobile) {
  const re = /^[6-9]\d{9}$/;
  return re.test(mobile);
}

/**
 * Searches for a word in sentence array and returns its index
 * Practical 2 requirement
 */
function searchWordInSentence(sentenceArray, word) {
  const target = word.toLowerCase();
  for (let i = 0; i < sentenceArray.length; i++) {
    if (sentenceArray[i].toLowerCase() === target) {
      return i;
    }
  }
  return -1;
}

/**
 * Finds position of first occurrence of vowel 'a', last occurrence of 'a',
 * and returns string between them.
 * Practical 3 requirement
 */
function extractBetweenVowels(str, vowel = 'a') {
  const lower = str.toLowerCase();
  const firstPos = lower.indexOf(vowel);
  const lastPos = lower.lastIndexOf(vowel);

  if (firstPos === -1 || lastPos === -1 || firstPos === lastPos) {
    return {
      firstPosition: firstPos,
      lastPosition: lastPos,
      subString: null
    };
  }

  return {
    firstPosition: firstPos + 1, // 1-based index
    lastPosition: lastPos + 1,
    subString: str.substring(firstPos + 1, lastPos)
  };
}

