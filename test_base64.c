#include <stdio.h>
#include <string.h>
#include <assert.h>

#include "base64.h"

#define TEST_ENCODE_STR(s) printf("ENCODE %s => %s\n", s, base64encode(s, strlen(s)))
#define TEST_DECODE_STR(s) do { \
	void *raw; int n; \
	int c = base64decode(s, &raw, &n); \
	assert(c == 0); \
	printf("DECODE %s => %s\n", s, raw); \
} while(0)

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
	TEST_DECODE_STR("YWJj");
	TEST_DECODE_STR("YWJjZA==");
	TEST_DECODE_STR("YWJjZGU=");
	// for (int i = 0; i < 256; ++i)
	// {
	// 	printf("%d => %d\n", i, i >> 2);
	// }
	return 0;
}
