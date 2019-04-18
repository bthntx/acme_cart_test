Product catalogue, delivery charge rules, and offers are stored in JSON files.

I assume that shipping charges array sorted by moreThan ASC already.

I assume that product code is uniq key, and safe as string to use it as key in associate array.

How it works: 
  - Adding items to array of items, if item already exist in this item increment qty.
  - When calculating total - we run through each item (even with same code) and trying to apply offer price modifier
  - After that we calculate shipping charges based on previous summ.
  - Return total value
  
  
