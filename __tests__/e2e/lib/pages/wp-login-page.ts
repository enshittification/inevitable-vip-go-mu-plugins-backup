import type { Page } from '@playwright/test';

const selectors = {
	userField: '#user_login',
	passwordField: '#user_pass',
	submitButton: '#wp-submit',
	lostPasswordLink: '#nav a[href*="wp-login.php?action=lostpassword"]',
};

export class LoginPage {
	private page: Page;

	/**
	 * Constructs an instance of the component.
	 *
	 * @param { Page } page The underlying page
	 */
	constructor( page: Page ) {
		this.page = page;
	}

	/**
	 * Navigate to login page
	 */
	public visit(): Promise<unknown> {
		return this.page.goto( '/wp-login.php' );
	}

	/**
	 * Logs in to account with specified username and password
	 *
	 * @param {string} username Username to login as
	 * @param {string} password Password for account
	 */
	public async login( username: string, password: string ): Promise<unknown> {
		await this.page.fill( selectors.userField, username );
		await this.page.fill( selectors.passwordField, password );
		return Promise.all( [ this.page.waitForURL( '**/wp-admin/**' ), this.page.click( selectors.submitButton ) ] );
	}

	public lostPassword(): Promise<unknown> {
		return Promise.all( [
			this.page.waitForURL( /\/wp-login\.php\?action=lostpassword/ ),
			this.page.locator( selectors.lostPasswordLink ).click(),
		] );
	}
}
