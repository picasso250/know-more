/*
In rfc2045

     Value Encoding  Value Encoding  Value Encoding  Value Encoding
         0 A            17 R            34 i            51 z
         1 B            18 S            35 j            52 0
         2 C            19 T            36 k            53 1
         3 D            20 U            37 l            54 2
         4 E            21 V            38 m            55 3
         5 F            22 W            39 n            56 4
         6 G            23 X            40 o            57 5
         7 H            24 Y            41 p            58 6
         8 I            25 Z            42 q            59 7
         9 J            26 a            43 r            60 8
        10 K            27 b            44 s            61 9
        11 L            28 c            45 t            62 +
        12 M            29 d            46 u            63 /
        13 N            30 e            47 v
        14 O            31 f            48 w         (pad) =
        15 P            32 g            49 x
        16 Q            33 h            50 y
*/
#include <stdlib.h>
#include <string.h>

char *char_table = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/";
char char_table_flip[128];

char *error_table[] = {
	"OK",
	"Length is not 4 or 4's", // 1
	"Empty string", // 2
	"not enough memory", // 3
};

char * base64encode(void * s, int len)
{
	// printf("encode '%s'(%d)\n", s, len);
	if (len <= 0)
	{
		return NULL;
	}
	int n;
	if (len % 3 == 0)
	{
		n = len / 3;
	} else {
		n = len / 3 + 1;
	}
	char *ret = malloc(sizeof(char)*n*4+1); // string must have an end
	if (ret == NULL)
	{
		return NULL;
	}

	// expand 3 bytes to 4 bytes
	char *sp = (char *)s;
	int i;
	for (i = 0; i < n; ++i)
	{
		unsigned a0 = sp[i*3];
		unsigned a1 = i*3+1 >= len ? 0 : sp[i*3+1]; // 0 for padding
		unsigned a2 = i*3+2 >= len ? 0 : sp[i*3+2];
		// printf("('%c', 0x%x) ('%c', 0x%x) ('%c', 0x%x)\n", a0, a0, a1, a1, a2, a2);
		int i0, i1, i2, i3;
		char b0, b1, b2, b3;
		i0 = a0 >> 2;
		i1 = ((a0 & 0x3) << 4) | (a1 >> 4);
		i2 = ((a1 & 0xF) << 2) | (a2 >> 6);
		i3 = a2 & 0x3F;
		ret[i*4+0] = b0 = char_table[i0];
		ret[i*4+1] = b1 = char_table[i1];
		ret[i*4+2] = b2 = char_table[i2];
		ret[i*4+3] = b3 = char_table[i3];
		// ret[i*4+4] = 0;
		// printf("('%c',%d) ('%c',%d) ('%c',%d) ('%c',%d)\n", b0, i0, b1, i1, b2, i2, b3, i3);
		// printf("%s\n", ret+i*4);

		// 如果最后剩下两个输入数据，在编码结果后加1个“=”；
		// 如果最后剩下一个输入数据，编码结果后加2个“=”；
		if (i*3+1 >= len)
		{
			ret[i*4+2] = '=';
			ret[i*4+3] = '=';
		}
		else if (i*3+2 >= len)
		{
			ret[i*4+3] = '=';
		}
	}
	ret[i*4] = 0;
	return ret;
}
void init_char_table_flip()
{
	for (int i = 0; i < strlen(char_table); ++i)
	{
		char_table_flip[char_table[i]] = i;
	}
	char_table_flip['='] = 0;
}
// return error code, 0 is OK
int base64decode(char *src, void **data_p, int *size_p)
{
	int len = strlen(src);
	if (len == 0) {
		return 2;
	}
	if (len % 4 != 0)
	{
		return 1;
	}
	int size = len/4*3;
	char *ret = malloc(sizeof(char)*size);
	if (ret == NULL)
	{
		return 3;
	}
	init_char_table_flip();
	// assume no padding
	for (int i = 0; i < len/4; ++i)
	{
		unsigned a0 = char_table_flip[ src[i*4+0] ];
		unsigned a1 = char_table_flip[ src[i*4+1] ];
		unsigned a2 = char_table_flip[ src[i*4+2] ];
		unsigned a3 = char_table_flip[ src[i*4+3] ];
		char b0, b1, b2;
		ret[i*3+0] = b0 = (char) (a0 << 2) | a1 >> 4;
		ret[i*3+1] = b1 = (char) ((a1 & 0xF) << 4) | (a2 >> 2);
		ret[i*3+2] = b2 = (char) ((a2 & 0x3) << 6) | a3;
	}
	*data_p = ret;
	if (src[len-2] == '=') // two =
	{
		size -= 2;
	}
	if (src[len-1] == '=') // only one =
	{
		size -= 1;
	}
	*size_p = size;
	return 0;
}
