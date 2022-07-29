<?PHP

class cug__pagination {
	private $items_total; //Total Items
	private $items_per_page;
	private $page_num; //Current Page Number
	private $page_buttons_total; //Pagination Buttons Number
	
	private $page_button_middle; //Middle Button's Number
	private $page_buttons_even_or_odd;
	
	public $pages_total			= 0;
	public $first_button_num 	= 0;
	public $last_button_num 	= 0;
	
	public $active_button_num 	= 0;
	public $next_button_num 	= 0;
	public $prev_button_num 	= 0;

	
	public function __construct($items_total, $items_per_page, $page_num, $page_buttons_total=10) {
		$this->items_total 			= $items_total;
		$this->items_per_page 		= $items_per_page;
		$this->page_num 			= $page_num;
		$this->page_buttons_total 	= $page_buttons_total;
		
			if($this->page_buttons_total > 0) {
				$this->page_button_middle = ceil($this->page_buttons_total / 2);
				
					if($this->page_buttons_total % 2 == 0) { //Even
						$this->page_button_middle += 1;
						$this->page_buttons_even_or_odd = 2;
					}
					else {
						$this->page_buttons_even_or_odd = 1; //Odd
					}

				// Calculate Total Pages Number	
				$this->pages_total = ceil($this->items_total / $this->items_per_page);
				
				//correct $this->page_num
				$this->page_num = ($this->page_num > $this->pages_total) ? $this->pages_total : $this->page_num; 
				$this->page_num = ($this->page_num <= 0) ? 1 : $this->page_num;
				
				// Calculate First and Last Button Numbers
					if($this->page_num <= $this->page_button_middle) {
						$this->first_button_num = 1;
						$this->last_button_num = ($this->pages_total < ($this->page_num + ($this->page_buttons_total - $this->page_num))) ? $this->pages_total : $this->page_buttons_total;
					}
					else {
						$this->first_button_num = $this->page_num - ($this->page_button_middle - 1);
						$this->last_button_num = ($this->pages_total < ($this->page_num + ($this->page_button_middle - $this->page_buttons_even_or_odd)) ) ? $this->pages_total : $this->page_num + ($this->page_button_middle - $this->page_buttons_even_or_odd);
						
						//correct $this->first_button_num
						$this->first_button_num = (($this->last_button_num - $this->first_button_num + 1) < $this->page_buttons_total) ? $this->last_button_num - $this->page_buttons_total + 1 : $this->first_button_num;
						
						if($this->first_button_num == 0) $this->first_button_num = 1;
					}
					
				// Calculate Active Button Number
					if($this->page_num > $this->pages_total)	
						$this->active_button_num = $this->last_button_num;
					elseif($this->page_num < 1)
						$this->active_button_num = 1;
					else 
						$this->active_button_num = $this->page_num;
					
				// Next/Prev Buttons
				$this->prev_button_num = ($this->page_num > 1) ? $this->page_num - 1 : 0;
				$this->next_button_num = ($this->page_num < $this->pages_total) ? $this->page_num + 1 : 0;
			}
	}
}
?>