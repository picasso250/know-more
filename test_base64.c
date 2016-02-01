#include <stdio.h>
#include <string.h>

#include "base64.h"

#define TEST_ENCODE_STR(s) printf("%s\n", base64encode(s, strlen(s)))

int main(int argc, char const *argv[])
{
	// for (int i = 'A'; i <= 'Z'; ++i)
	// {
	// 	printf("%c", i);
	// }
	// for (int i = 'a'; i <= 'z'; ++i)
	// {
	// 	printf("%c", i);
	// }
	// for (int i = '0'; i <= '9'; ++i)
	// {
	// 	printf("%c", i);
	// }
	// printf("\n");
	TEST_ENCODE_STR("abc");   // YWJj
	TEST_ENCODE_STR("abcd");  // YWJjZA==
	TEST_ENCODE_STR("abcde"); // YWJjZGU=
	// for (int i = 0; i < 256; ++i)
	// {
	// 	printf("%d => %d\n", i, i >> 2);
	// }
	return 0;
}
